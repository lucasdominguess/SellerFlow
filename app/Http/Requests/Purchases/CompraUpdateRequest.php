<?php

namespace App\Http\Requests\Purchases;

use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompraUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // identidade (company_id, store_id, user_id) é imutável e não vem do cliente.
        // 'atrasado' é estado exclusivo do financeiro — não pode ser definido na compra.
        return [
            'fornecedor_id'      => ['sometimes', 'integer', 'exists:fornecedores,id'],
            'forma_pagamento_id' => ['sometimes', 'integer', 'exists:forma_pagamentos,id'],
            'status'             => ['sometimes', Rule::enum(TransactionStatus::class)->except([TransactionStatus::OVERDUE])],
            'numero_nota'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'data_compra'        => ['sometimes', 'date'],
            'numero_parcelas'    => ['sometimes', 'integer', 'min:1'],
            'observacao'         => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists'  => 'O campo :attribute deve existir na tabela :table.',
            'string'  => 'O campo :attribute deve ser uma string.',
            'max'     => 'O campo :attribute deve ter no máximo :max caracteres.',
            'date'    => 'O campo :attribute deve ser uma data válida.',
            'min'     => 'O campo :attribute deve ser maior ou igual a :min.',
            'enum'    => 'O valor selecionado para :attribute é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'fornecedor_id'      => 'fornecedor',
            'forma_pagamento_id' => 'forma de pagamento',
            'status'             => 'status',
            'numero_nota'        => 'número da nota',
            'data_compra'        => 'data da compra',
            'numero_parcelas'    => 'número de parcelas',
            'observacao'         => 'observação',
        ];
    }
}
