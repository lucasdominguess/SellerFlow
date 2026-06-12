<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashFlowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'granularity' => ['sometimes', Rule::in(['day', 'week', 'month'])],
        ];
    }

    public function messages(): array
    {
        return [
            'required'         => 'O campo :attribute é obrigatório.',
            'date'             => 'O campo :attribute deve ser uma data válida.',
            'after_or_equal'   => 'O campo :attribute deve ser igual ou posterior a :date.',
            'granularity.in'   => 'A granularidade deve ser day, week ou month.',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date'  => 'data inicial',
            'end_date'    => 'data final',
            'granularity' => 'granularidade',
        ];
    }
}
