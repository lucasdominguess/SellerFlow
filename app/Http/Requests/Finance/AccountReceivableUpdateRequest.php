<?php

namespace App\Http\Requests\Finance;

use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountReceivableUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor' => ['sometimes', 'numeric', 'min:0'],
            'previsao_recebimento' => ['sometimes', 'nullable', 'date'],
            'recebido_em' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', Rule::enum(TransactionStatus::class)],
            'observacao' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'enum' => 'O valor selecionado para :attribute é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'valor' => 'valor',
            'previsao_recebimento' => 'previsão de recebimento',
            'recebido_em' => 'recebido em',
            'status' => 'status',
            'observacao' => 'observação',
        ];
    }
}
