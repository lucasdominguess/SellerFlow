<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Período é opcional: ausente, o DTO assume o mês corrente.
        return [
            'start_date' => ['sometimes', 'date'],
            'end_date'   => ['sometimes', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'date'           => 'O campo :attribute deve ser uma data válida.',
            'after_or_equal' => 'O campo :attribute deve ser igual ou posterior a :date.',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => 'data inicial',
            'end_date'   => 'data final',
        ];
    }
}
