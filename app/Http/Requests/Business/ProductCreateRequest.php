<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku'          => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name'         => ['required', 'string', 'max:255'],
            'marca'        => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:255'],
            'price_unit'   => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'price_box'    => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'status_id'    => ['required', 'integer', 'exists:status,id'],
            'fornecedor_id'=> ['nullable', 'integer', 'exists:fornecedores,id'],
            'path_image'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser uma string.',
            'max'      => 'O campo :attribute deve ter no máximo :max caracteres.',
            'unique'   => 'O :attribute informado já está em uso.',
            'numeric'  => 'O campo :attribute deve ser um número.',
            'min'      => 'O campo :attribute deve ser maior ou igual a :min.',
            'decimal'  => 'O campo :attribute deve ter no máximo 2 casas decimais.',
            'integer'  => 'O campo :attribute deve ser um número inteiro.',
            'exists'   => 'O :attribute informado não existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sku'           => 'SKU',
            'name'          => 'nome',
            'marca'         => 'marca',
            'description'   => 'descrição',
            'price_unit'    => 'preço unitário',
            'price_box'     => 'preço por caixa',
            'status_id'     => 'status',
            'fornecedor_id' => 'fornecedor',
            'path_image'    => 'imagem',
        ];
    }
}
