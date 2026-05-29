<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

use Normalizer;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'min:8', 'same:password'],

            'company_name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18', 'unique:companies,cnpj'],
            'description' => ['nullable', 'string', 'max:255'],


        ];
    }
    public function messages(): array
    {
        return [
            '*.string' => 'O campo :attribute deve ser uma string.',
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'O email já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'confirm_password.required' => 'O campo confirmação de senha é obrigatório.',
            'confirm_password.min' => 'A confirmação de senha deve conter no mínimo 8 caracteres.',
            'confirm_password.same' => 'A confirmação de senha deve ser igual à senha.',

            'company_name.required' => 'O campo nome da empresa é obrigatório.',
            'cnpj.nullable' => 'O campo CNPJ é opcional.',
            'cnpj.unique' => 'O CNPJ já está em uso.',
            'description.max' => 'A descrição deve conter no máximo 255 caracteres.',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'password' => 'senha',
            'confirm_password' => 'confirmação de senha',
            'company_name' => 'nome da empresa',
            'cnpj' => 'CNPJ',
            'description' => 'descrição',
        ];
    }


        public function userData(): array
    {
        return $this->only(['name', 'email', 'password']);
    }

    public function companyData(): array
    {
        return [
            'name'        => $this->input('company_name'),
            'cnpj'        => $this->input('cnpj'),
            'description' => $this->input('description'),
        ];
    }
}
