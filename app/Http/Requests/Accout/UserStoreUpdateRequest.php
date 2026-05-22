<?php

namespace App\Http\Requests\Accout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentId = $this->route('user_store')?->id;

        return [
            'user_id' => [
                'sometimes', 'integer', 'exists:users,id',
                Rule::unique('user_stores')
                    ->where('store_id', $this->store_id ?? $this->route('user_store')?->store_id)
                    ->ignore($currentId),
            ],
            'store_id' => ['sometimes', 'integer', 'exists:stores,id'],
            'role_id' => ['sometimes', 'integer', 'exists:roles,id'],
            'status_id' => ['sometimes', 'integer', 'exists:status,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'user_id.unique' => 'Este usuário já está vinculado a esta loja.',
        ];
    }
    public function attributes(): array
    {
        return [
            'user_id' => 'usuário',
            'store_id' => 'loja',
            'role_id' => 'permissão',
            'status_id' => 'status',
        ];
    }
}
