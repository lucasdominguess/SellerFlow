<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class FornecedorCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'responsavel'  => ['nullable', 'string', 'max:255'],
            'cnpj'         => ['required', 'string', 'max:18', 'unique:fornecedores,cnpj'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:fornecedores,email'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string', 'max:255'],
            'link_catalog' => ['nullable', 'string', 'url', 'max:255'],
            'description'  => ['nullable', 'string', 'max:255'],
            'status_id'    => ['required', 'integer', 'exists:status,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser uma string.',
            'max'      => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email'    => 'O campo :attribute deve ser um e-mail válido.',
            'url'      => 'O campo :attribute deve ser uma URL válida.',
            'unique'   => 'O :attribute informado já está em uso.',
            'integer'  => 'O campo :attribute deve ser um número inteiro.',
            'exists'   => 'O :attribute informado não existe.',
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
