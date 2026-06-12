<?php

namespace App\Http\Requests\Purchases;

use App\Classes\AuthContext;
use App\Enums\OriginType;
use Illuminate\Foundation\Http\FormRequest;

class CompraCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'store_id' => 'required|integer|exists:stores,id',
            'fornecedor_id' => 'required|integer|exists:fornecedores,id',
            'user_id' => 'required|integer|exists:users,id',
            'forma_pagamento_id' => 'required|integer|exists:forma_pagamentos,id',
            // status não vem do cliente no create: a compra sempre nasce 'pendente'
            'numero_nota' => 'nullable|string|max:255',
            'data_compra' => 'required|date',
            // 'valor_total' => 'required|numeric|min:0',
            'numero_parcelas' => 'nullable|integer|min:1',
            'observacao' => 'nullable|string|max:1000',

            'itens' => 'required|array|min:1',
            'itens.*.product_id' => 'required|integer|exists:products,id',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.valor_unitario' => 'required|numeric|min:0',

        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'date' => 'O campo :attribute deve ser uma data válida.',

            'itens.required' => 'A compra deve conter pelo menos um item.',
            'itens.array' => 'O campo itens deve ser um array.',
            'itens.min' => 'A compra deve conter pelo menos um item.',
            'itens.*.product_id.required' => 'O campo product_id é obrigatório para cada item.',
            'itens.*.product_id.integer' => 'O campo product_id deve ser um número inteiro para cada item.',
            'itens.*.product_id.exists' => 'O campo product_id deve existir na tabela products para cada item.',
            'itens.*.quantidade.required' => 'O campo quantidade é obrigatório para cada item.',
            'itens.*.quantidade.integer' => 'O campo quantidade deve ser um número inteiro para cada item.',
            'itens.*.quantidade.min' => 'O campo quantidade deve ser maior ou igual a 1 para cada item.',
            'itens.*.valor_unitario.required' => 'O campo valor_unitario é obrigatório para cada item.',
            'itens.*.valor_unitario.numeric' => 'O campo valor_unitario deve ser um número para cada item.',
            'itens.*.valor_unitario.min' => 'O campo valor_unitario deve ser maior ou igual a 0 para cada item.',

        ];
    }

    public function attributes(): array
    {
        return [
            'fornecedor_id' => 'fornecedor',
            'forma_pagamento_id' => 'forma de pagamento',
            'numero_nota' => 'número da nota',
            'data_compra' => 'data da compra',
            'valor_total' => 'valor total',
            'numero_parcelas' => 'número de parcelas',
            'observacao' => 'observação',

            'itens' => 'itens da compra',
            'itens.*.product_id' => 'produto',
            'itens.*.quantidade' => 'quantidade',
            'itens.*.valor_unitario' => 'valor unitário',
        ];
    }
    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'user_id' => AuthContext::userId(),
            'company_id' => AuthContext::companyIds()->first(),
            'store_id' => AuthContext::storeIds()->first(),
        ]);
    }

}
