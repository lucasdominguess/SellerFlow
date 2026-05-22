<?php

namespace App\Http\Requests\Accout;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'status_id' => ['required', 'integer', 'exists:status,id'],
            'marketplace_id' => ['required', 'integer', 'exists:marketplaces,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve ser um email válido.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'description' => 'descrição',
            'status_id' => 'status',
            'marketplace_id' => 'marketplace',
            'company_id' => 'empresa',
        ];
    }
}
