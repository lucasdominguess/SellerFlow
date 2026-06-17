<?php

namespace App\Http\Requests\Finance;

use App\Classes\AuthContext;
use App\Enums\OriginType;
use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountPayableCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'valor' => ['required', 'numeric', 'min:0'],
            'vencimento' => ['nullable', 'date'],
            'pago_em' => ['nullable', 'date'],
            'status' => ['nullable', Rule::enum(TransactionStatus::class)],
            'categoria_financeira_id' => ['nullable', 'integer', 'exists:financial_categories,id'],
            'forma_pagamento_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'origem_tipo' => ['nullable', Rule::enum(OriginType::class)],
            'origem_id' => ['nullable', 'integer'],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'min' => 'O campo :attribute deve ser maior ou igual a :min.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'exists' => 'O campo :attribute deve existir na tabela :table.',
            'enum' => 'O valor selecionado para :attribute é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'valor' => 'valor',
            'vencimento' => 'vencimento',
            'pago_em' => 'pago em',
            'status' => 'status',
            'categoria_financeira_id' => 'categoria financeira',
            'forma_pagamento_id' => 'forma de pagamento',
            'origem_tipo' => 'origem',
            'origem_id' => 'id de origem',
            'observacao' => 'observação',
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'company_id' => AuthContext::companyIds()->first(),
        ]);
    }
}
