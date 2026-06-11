<?php

namespace App\Http\Requests\Adjustment;

use App\Classes\AuthContext;
use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],

            'itens' => ['required', 'array', 'min:1'],

            'itens.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'itens.*.quantidade' => ['required', 'integer', 'not_in:0'],
            'itens.*.motivo' => ['required', 'string', 'in:perda,quebra,contagem_fisica,devolucao,outro'],
            'itens.*.observacao' => ['nullable', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'string' => 'O campo :attribute deve ser uma string.',
            'array' => 'O campo :attribute deve ser um array.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'itens.*.quantidade.not_in' => 'A quantidade não pode ser zero. Use um valor positivo para ajuste de entrada ou negativo para ajuste de saída.',
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
