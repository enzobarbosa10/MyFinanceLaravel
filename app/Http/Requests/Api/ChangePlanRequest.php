<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ChangePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'plan' => ['required', 'string', 'exists:plans,slug'],
            'gateway' => ['nullable', 'string', 'in:stripe,pagseguro'],
        ];
    }
}
