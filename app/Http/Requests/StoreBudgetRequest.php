<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|regex:/^\d{4}-\d{2}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Selecione uma categoria.',
            'category_id.exists' => 'A categoria selecionada é inválida.',
            'amount.required' => 'Informe o valor do orçamento.',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor mínimo é R$ 0,01.',
            'month.required' => 'Informe o mês de referência.',
            'month.regex' => 'O formato do mês deve ser AAAA-MM.',
        ];
    }
}
