<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AskAssistantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'A pergunta é obrigatória.',
            'question.min'      => 'A pergunta deve ter pelo menos 5 caracteres.',
            'question.max'      => 'A pergunta deve ter no máximo 500 caracteres.',
        ];
    }
}
