<?php

namespace App\Http\Requests\Accout;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirmed_password' => ['required', 'string', 'min:8', 'same:password'],
            'status_id' => ['required', 'integer', 'exists:status,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve ser um email válido.',
            'unique' => 'O campo :attribute já está em uso.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'same' => 'O campo :attribute deve ser igual ao campo :other.',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'password' => 'senha',
            'confirmed_password' => 'confirmação de senha',
            'status_id' => 'status',
        ];
    }
}
