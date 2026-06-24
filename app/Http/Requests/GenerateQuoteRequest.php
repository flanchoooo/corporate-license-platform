<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canWriteCorporateData() ?? false;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'include_carbon_tax' => ['nullable', 'boolean'],
            'insurance_type' => ['nullable', 'in:third_party,full_cover'],
        ];
    }
}
