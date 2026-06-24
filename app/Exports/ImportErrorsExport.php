<?php

namespace App\Exports;

use App\Models\VehicleImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportErrorsExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly VehicleImport $import)
    {
    }

    public function headings(): array
    {
        return ['Row', 'Number Plate', 'Error', 'Payload'];
    }

    public function collection(): Collection
    {
        return $this->import->errors->map(fn ($error) => [
            $error->row_number,
            $error->number_plate,
            $error->message,
            json_encode($error->row_payload),
        ]);
    }
}
