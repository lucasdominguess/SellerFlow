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

    protected function prepareForValidation(): void
    {
        $perPage = $this->query('perPage');
        $page    = $this->query('page');

        $this->merge([
            'perPage' => ($perPage === '' || $perPage === null) ? 15 : (int) $perPage,
            'page'    => ($page === '' || $page === null) ? 1 : (int) $page,
        ]);
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'product_name' => ['nullable', 'string'],
            'sku' => ['nullable', 'string'],
            'perPage' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'company_id' => AuthContext::companyIds()->first(),
        ]);
    }
}
