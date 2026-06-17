<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FornecedorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentId = $this->route('fornecedor')?->id;

        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'responsavel'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'cnpj'         => ['sometimes', 'string', 'max:18', Rule::unique('suppliers', 'cnpj')->ignore($currentId)],
            'email'        => ['sometimes', 'string', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($currentId)],
            'phone'        => ['sometimes', 'nullable', 'string', 'max:20'],
            'address'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'link_catalog' => ['sometimes', 'nullable', 'string', 'url', 'max:255'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:255'],
            'status_id'    => ['sometimes', 'integer', 'exists:status,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'string'  => 'O campo :attribute deve ser uma string.',
            'max'     => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email'   => 'O campo :attribute deve ser um e-mail válido.',
            'url'     => 'O campo :attribute deve ser uma URL válida.',
            'unique'  => 'O :attribute informado já está em uso.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists'  => 'O :attribute informado não existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'         => 'nome',
            'responsavel'  => 'responsável',
            'cnpj'         => 'CNPJ',
            'email'        => 'e-mail',
            'phone'        => 'telefone',
            'address'      => 'endereço',
            'link_catalog' => 'link do catálogo',
            'description'  => 'descrição',
            'status_id'    => 'status',
        ];
    }
}
