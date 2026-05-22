<?php

namespace App\Http\Requests\Accout;

use App\Rules\Cnpj;
use Illuminate\Foundation\Http\FormRequest;

class CompanyCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'name' => ['required', 'string', 'max:255'],
           'cnpj' => ['required', 'string', 'max:20', 'unique:companies,cnpj',new Cnpj()],
           'description' => ['nullable', 'string', 'max:255'],
           'status_id' => ['required', 'integer', 'exists:status,id'],
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
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'cnpj' => 'CNPJ',
            'status_id' => 'status',
            'description' => 'descrição',

        ];
    }
    public function prepareForValidation()
    {
        //normalizar cnpj

        if ($this->cnpj) {
            $cnpj = preg_replace('/\D/', '', $this->cnpj);
            $this->merge(['cnpj' => $cnpj]);
        }
    }
}
