<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'icon' => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da meta.',
            'name.max' => 'O nome pode ter no máximo 100 caracteres.',
            'target_amount.required' => 'Informe o valor da meta.',
            'target_amount.numeric' => 'O valor deve ser numérico.',
            'target_amount.min' => 'O valor mínimo da meta é R$ 1,00.',
            'deadline.required' => 'Informe o prazo da meta.',
            'deadline.date' => 'O prazo informado é inválido.',
            'deadline.after' => 'O prazo deve ser uma data futura.',
            'icon.max' => 'O ícone pode ter no máximo 10 caracteres.',
        ];
    }
}
