<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canWriteCorporateData() ?? false;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'number_plate' => ['required', 'string', 'max:30', Rule::unique('vehicles', 'number_plate')->ignore($vehicleId)],
            'engine_capacity' => ['required', 'integer', 'min:1', 'max:20000'],
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'vehicle_type' => ['required', 'string', 'max:80'],
            'chassis_number' => ['nullable', 'string', 'max:120'],
            'vin' => ['nullable', 'string', 'max:120'],
            'fuel_type' => ['nullable', 'string', 'max:40'],
            'owner_name' => ['nullable', 'string', 'max:150'],
            'last_license_expires_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('number_plate')) {
            $this->merge(['number_plate' => strtoupper((string) $this->number_plate)]);
        }
    }
}
