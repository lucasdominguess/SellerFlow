<?php

namespace App\Http\Requests\Sales;

use App\Classes\AuthContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendasCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // store_id, user_id e company_id NÃO entram aqui: vêm do JWT (AuthContext) no DTO.
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],

            'market_place_id' => ['required', 'integer', 'exists:market_places,id'],
            'numero_pedido' => [
                'required',
                'string',
                'max:255',
                // unique composto: numero_pedido é único por marketplace (unique[market_place_id, numero_pedido])
                Rule::unique('sales', 'numero_pedido')
                    ->where(fn($query) => $query->where('market_place_id', $this->market_place_id)),
            ],
            'data_venda' => ['required', 'date'],
            'valor_bruto' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'taxa_marketplace' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'valor_frete' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'data_previsao_repasse' => ['nullable', 'date'],
            'observacao' => ['nullable', 'string'],
            'venda_itens' => ['required', 'array', 'min:1'],
            'venda_itens.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'venda_itens.*.quantidade' => ['required', 'integer', 'min:1'],
            'venda_itens.*.valor_unitario' => ['required', 'numeric', 'min:0', 'decimal:0,2'],



        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'unique' => 'O campo :attribute já está em uso.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'decimal' => 'O campo :attribute deve ter no máximo 2 casas decimais.',
            'date' => 'O campo :attribute deve ser uma data válida.',

            'venda_itens.required' => 'A venda deve conter pelo menos um item.',
            'venda_itens.array' => 'O campo venda_itens deve ser um array.',
            'venda_itens.min' => 'A venda deve conter pelo menos um item.',
            'venda_itens.*.product_id.required' => 'O campo product_id é obrigatório para cada item.',
            'venda_itens.*.product_id.integer' => 'O campo product_id deve ser um número inteiro para cada item.',
            'venda_itens.*.product_id.exists' => 'O campo product_id deve existir na tabela products para cada item.',
            'venda_itens.*.quantidade.required' => 'O campo quantidade é obrigatório para cada item.',
            'venda_itens.*.quantidade.integer' => 'O campo quantidade deve ser um número inteiro para cada item.',
            'venda_itens.*.quantidade.min' => 'O campo quantidade deve ser maior ou igual a 1 para cada item.',
            'venda_itens.*.valor_unitario.required' => 'O campo valor_unitario é obrigatório para cada item.',
            'venda_itens.*.valor_unitario.numeric' => 'O campo valor_unitario deve ser um número para cada item.',
            'venda_itens.*.valor_unitario.min' => 'O campo valor_unitario deve ser maior ou igual a 0 para cada item.',
            'venda_itens.*.valor_unitario.decimal' => 'O campo valor_unitario deve ter no máximo 2 casas decimais para cada item.',
        ];
    }

    public function attributes(): array
    {
        return [
            'market_place_id' => 'ID do marketplace',
            'numero_pedido' => 'número do pedido',
            'data_venda' => 'data da venda',
            'valor_bruto' => 'valor bruto',
            'taxa_marketplace' => 'taxa do marketplace',
            'valor_frete' => 'valor do frete',
            'data_previsao_repasse' => 'data de previsão de repasse',
            'observacao' => 'observação',
            'venda_itens' => 'itens da venda',
            'venda_itens.*.product_id' => 'ID do produto',
            'venda_itens.*.quantidade' => 'quantidade',
            'venda_itens.*.valor_unitario' => 'valor unitário',
        ];
    }

    protected function prepareForValidation(): void
    {
        //
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
