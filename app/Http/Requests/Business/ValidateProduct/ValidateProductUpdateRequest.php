<?php

namespace App\Http\Requests\Business\ValidateProduct;

use Illuminate\Foundation\Http\FormRequest;

class ValidateProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}
