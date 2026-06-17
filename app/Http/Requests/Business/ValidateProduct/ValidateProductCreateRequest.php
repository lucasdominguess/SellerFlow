<?php

namespace App\Http\Requests\Business\ValidateProduct;

use App\Classes\AuthContext;
use Illuminate\Foundation\Http\FormRequest;

class ValidateProductCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
    {
        // Identidade (company_id, user_id) vem do contexto autenticado.
        // Valores derivados (profit_*, breakeven_roas, fee_*) são calculados
        // pelo Service — jamais aceitos do cliente.
        return [
            'name'            => ['required', 'string', 'max:255'],
            'brand'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'description'     => ['sometimes', 'nullable', 'string', 'max:1000'],
            'catalog_link'    => ['sometimes', 'nullable', 'url', 'max:2048'],
            'fornecedor_id'   => ['sometimes', 'nullable', 'integer', 'exists:fornecedores,id'],
            'price_sale'      => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'price_buy'       => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'cust_additional' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'marketplace_id'  => ['required', 'integer', 'exists:market_places,id'],

            'company_id'      => ['required', 'integer', 'exists:companies,id'],
            'user_id'         => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'      => 'O campo :attribute é obrigatório.',
            'string'        => 'O campo :attribute deve ser uma string.',
            'integer'       => 'O campo :attribute deve ser um número inteiro.',
            'numeric'       => 'O campo :attribute deve ser um número.',
            'url'           => 'O campo :attribute deve ser uma URL válida.',
            'exists'        => 'O campo :attribute deve existir na tabela :table.',
            'max'           => 'O campo :attribute deve ter no máximo :max caracteres.',
            'min'           => 'O campo :attribute deve ser maior ou igual a :min.',
            'price_sale.decimal'      => 'O preço de venda deve ter no máximo 2 casas decimais.',
            'price_buy.decimal'       => 'O preço de compra deve ter no máximo 2 casas decimais.',
            'cust_additional.decimal' => 'O custo de insumos deve ter no máximo 2 casas decimais.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'nome',
            'brand'           => 'marca',
            'description'     => 'descrição',
            'catalog_link'    => 'link do catálogo',
            'fornecedor_id'   => 'fornecedor',
            'price_sale'      => 'preço de venda',
            'price_buy'       => 'preço de compra',
            'cust_additional' => 'custo de insumos',
        ];
    }
      public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'company_id' => AuthContext::companyIds()->first(),
            'user_id' => AuthContext::userId(),
        ]);
    }
}
