<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'total_amount' => 'required|numeric|min:0.01',
            'monthly_interest_rate' => 'nullable|numeric|min:0',
            'min_payment' => 'nullable|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:31',
            'creditor' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da dívida.',
            'name.max' => 'O nome pode ter no máximo 100 caracteres.',
            'total_amount.required' => 'Informe o valor total da dívida.',
            'total_amount.numeric' => 'O valor deve ser numérico.',
            'total_amount.min' => 'O valor mínimo é R$ 0,01.',
            'monthly_interest_rate.numeric' => 'A taxa de juros deve ser numérica.',
            'monthly_interest_rate.min' => 'A taxa de juros não pode ser negativa.',
            'min_payment.numeric' => 'O pagamento mínimo deve ser numérico.',
            'min_payment.min' => 'O pagamento mínimo não pode ser negativo.',
            'due_day.required' => 'Informe o dia de vencimento.',
            'due_day.integer' => 'O dia de vencimento deve ser um número inteiro.',
            'due_day.min' => 'O dia de vencimento mínimo é 1.',
            'due_day.max' => 'O dia de vencimento máximo é 31.',
            'creditor.max' => 'O credor pode ter no máximo 100 caracteres.',
        ];
    }
}
