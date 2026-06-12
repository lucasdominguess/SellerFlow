<?php

namespace App\Http\Requests\Finance;

use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountPayableUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor' => ['sometimes', 'numeric', 'min:0'],
            'vencimento' => ['sometimes', 'nullable', 'date'],
            'pago_em' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', Rule::enum(TransactionStatus::class)],
            'categoria_financeira_id' => ['sometimes', 'nullable', 'integer', 'exists:categoria_financeiras,id'],
            'forma_pagamento_id' => ['sometimes', 'nullable', 'integer', 'exists:forma_pagamentos,id'],
            'observacao' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'enum' => 'O valor selecionado para :attribute é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'valor' => 'valor',
            'vencimento' => 'vencimento',
            'pago_em' => 'pago em',
            'status' => 'status',
            'categoria_financeira_id' => 'categoria financeira',
            'forma_pagamento_id' => 'forma de pagamento',
            'observacao' => 'observação',
        ];
    }
}
