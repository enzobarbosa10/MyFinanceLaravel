<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_id'     => 'required|exists:accounts,id',
            'category_id'    => 'required|exists:categories,id',
            'type'           => ['required', Rule::enum(TransactionType::class)],
            'amount'         => 'required|numeric|min:0.01',
            'description'    => 'nullable|string|max:255',
            'transaction_at' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required'     => 'Selecione uma conta.',
            'account_id.exists'       => 'A conta selecionada é inválida.',
            'category_id.required'    => 'Selecione uma categoria.',
            'category_id.exists'      => 'A categoria selecionada é inválida.',
            'type.required'           => 'Informe o tipo da transação.',
            'amount.required'         => 'Informe o valor.',
            'amount.numeric'          => 'O valor deve ser numérico.',
            'amount.min'              => 'O valor mínimo é R$ 0,01.',
            'description.max'         => 'A descrição pode ter no máximo 255 caracteres.',
            'transaction_at.required' => 'Informe a data da transação.',
            'transaction_at.date'     => 'A data da transação é inválida.',
        ];
    }
}
