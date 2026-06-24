<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canWriteCorporateData() ?? false;
    }

    public function rules(): array
    {
        return [
            'mobile_number' => ['required', 'string', 'regex:/^\\+?2637[1378][0-9]{7}$/'],
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
        ];
    }
}
