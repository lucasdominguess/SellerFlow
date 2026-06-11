<?php

namespace App\Http\Requests\Stock;

use App\Classes\AuthContext;
use Illuminate\Foundation\Http\FormRequest;

class CheckStockQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'product_name' => ['nullable', 'string'],
            'sku' => ['nullable', 'string'],
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'company_id' => AuthContext::companyIds()->first(),
        ]);
    }
}
