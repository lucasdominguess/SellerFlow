<?php

namespace App\Http\Requests\Business\ValidateProduct;

use App\Classes\AuthContext;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ValidatedProductRequest extends FormRequest
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
            'price_sale' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'price_buy' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'cust_additional' => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'marketplace_id' => ['required', 'integer', 'exists:market_places,id'],
        ];
    }
    public function messages()
    {
        return [

            'marketplace_id.exists' => 'O marketplace especificado não existe.',
            '*.numeric' => 'O campo :attribute deve ser um número.',
            '*.min' => 'O campo :attribute deve ser maior ou igual a :min.',
            '*.decimal' => 'O campo :attribute deve ter no máximo 2 casas decimais.',
            '*.required' => 'O campo :attribute é obrigatório.',
            '*.integer' => 'O campo :attribute deve ser um número inteiro.',
        ];
    }
    public function attributes()
    {
        return [
            'price_sale' => 'Preço de venda',
            'price_buy' => 'Preço de compra',
            'cust_additional' => 'Custo de insumos',
            'marketplace_id' => 'Marketplace',
        ];
    }
  
}
