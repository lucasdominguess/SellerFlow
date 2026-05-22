<?php

namespace App\Http\Requests\Accout;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'description' => ['sometimes', 'string', 'max:255'],
            'status_id' => ['sometimes', 'integer', 'exists:status,id'],
            'marketplace_id' => ['sometimes', 'integer', 'exists:marketplaces,id'],
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
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
