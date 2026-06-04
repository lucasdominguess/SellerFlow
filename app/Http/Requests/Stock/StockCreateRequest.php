<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StockCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'product_id' => 'required|exists:products,id',
           'quantidade' => 'required|integer|min:1',
           'tipo' => 'required|in:entrada,saida,ajuste',
              'origem_tipo' => 'required|in:compra,venda,ajuste_manual',
              'origem_id' => 'required|integer',
              'observacao' => 'nullable|string|max:255',
            //   'user_id' => 'required|exists:users,id',
            //   'company_id' => 'required|exists:companies,id',
        ];
    }
    public function messages(): array
    {
        return [
            'product_id.required' => 'O campo product_id é obrigatório.',
            'product_id.exists' => 'O produto especificado não existe.',
            'quantidade.required' => 'O campo quantidade é obrigatório.',
            'quantidade.integer' => 'O campo quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.in' => 'O campo tipo deve ser um dos seguintes: entrada, saida, ajuste.',
            'origem_tipo.required' => 'O campo origem_tipo é obrigatório.',
            'origem_tipo.in' => 'O campo origem_tipo deve ser um dos seguintes: compra, venda, ajuste_manual.',
            'origem_id.required' => 'O campo origem_id é obrigatório.',
            'origem_id.integer' => 'O campo origem_id deve ser um número inteiro.',
            'observacao.string' => 'O campo observacao deve ser uma string.',
            'observacao.max' => 'O campo observacao não pode exceder 255 caracteres.',
            'user_id.required' => 'O campo user_id é obrigatório.',
            'user_id.exists' => 'O usuário especificado não existe.',
            'company_id.required' => 'O campo company_id é obrigatório.',
            'company_id.exists' => 'A empresa especificada não existe.',
        ];
    }
    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
        ]);
    }
}
