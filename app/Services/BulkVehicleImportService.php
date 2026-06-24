<?php

namespace App\Services;

use App\Imports\VehicleRowsImport;
use App\Models\Corporate;
use App\Models\Vehicle;
use App\Models\VehicleImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BulkVehicleImportService
{
    private const REQUIRED_COLUMNS = ['number_plate', 'engine_capacity', 'make', 'model', 'year', 'vehicle_type'];

    public function import(Corporate $corporate, int $userId, UploadedFile $file): VehicleImport
    {
        $reader = new VehicleRowsImport();
        Excel::import($reader, $file);

        $rows = $reader->rows ?? collect();
        $headers = $rows->shift()?->map(fn ($value) => Str::snake(trim((string) $value)))->all() ?? [];

        $batch = VehicleImport::create([
            'corporate_id' => $corporate->id,
            'uploaded_by' => $userId,
            'filename' => $file->getClientOriginalName(),
            'status' => 'completed',
        ]);

        $missing = array_diff(self::REQUIRED_COLUMNS, $headers);
        if ($missing !== []) {
            $batch->errors()->create([
                'row_number' => 1,
                'message' => 'Missing required columns: '.implode(', ', $missing),
                'row_payload' => $headers,
            ]);
            $batch->update(['failed_rows' => 1, 'status' => 'failed']);

            return $batch->fresh('errors');
        }

        $seen = [];
        $imported = 0;
        $failed = 0;

        foreach ($rows as $offset => $row) {
            $payload = array_combine($headers, $row->take(count($headers))->all());
            $payload = collect($payload)->map(fn ($value) => is_string($value) ? trim($value) : $value)->all();
            $payload['number_plate'] = Str::upper((string) ($payload['number_plate'] ?? ''));

            $validator = Validator::make($payload, [
                'number_plate' => ['required', 'string', 'max:30'],
                'engine_capacity' => ['required', 'integer', 'min:1', 'max:20000'],
                'make' => ['required', 'string', 'max:100'],
                'model' => ['required', 'string', 'max:100'],
                'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
                'vehicle_type' => ['required', 'string', 'max:80'],
            ]);

            $plate = $payload['number_plate'];
            $duplicate = in_array($plate, $seen, true) || Vehicle::where('number_plate', $plate)->exists();

            if ($validator->fails() || $duplicate) {
                $failed++;
                $batch->errors()->create([
                    'row_number' => $offset + 2,
                    'number_plate' => $plate,
                    'message' => $duplicate ? 'Duplicate number plate.' : $validator->errors()->first(),
                    'row_payload' => $payload,
                ]);
                continue;
            }

            $seen[] = $plate;

            Vehicle::create([
                'corporate_id' => $corporate->id,
                'number_plate' => $plate,
                'engine_capacity' => $payload['engine_capacity'],
                'make' => $payload['make'],
                'model' => $payload['model'],
                'year' => $payload['year'],
                'vehicle_type' => $payload['vehicle_type'],
                'owner_name' => $corporate->company_name,
            ]);

            $imported++;
        }

        $batch->update([
            'total_rows' => $imported + $failed,
            'imported_rows' => $imported,
            'failed_rows' => $failed,
            'status' => $failed > 0 ? 'completed_with_errors' : 'completed',
        ]);

        return $batch->fresh('errors');
    }
}
