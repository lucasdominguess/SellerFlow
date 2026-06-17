<?php

namespace App\Http\Requests\Sales;

use App\Enums\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendasUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentId = $this->route('venda')?->id;
        $marketPlaceId = $this->market_place_id ?? $this->route('venda')?->market_place_id;

        // identidade (store_id, user_id, company_id) é imutável e não vem do cliente.
        return [
            'market_place_id'       => ['sometimes', 'integer', 'exists:market_places,id'],
            'numero_pedido'         => [
                'sometimes', 'string', 'max:255',
                Rule::unique('sales', 'numero_pedido')
                    ->ignore($currentId)
                    ->where(fn ($query) => $query->where('market_place_id', $marketPlaceId)),
            ],
            'data_venda'            => ['sometimes', 'date'],
            'valor_bruto'           => ['sometimes', 'numeric', 'min:0', 'decimal:0,2'],
            'taxa_marketplace'      => ['sometimes', 'nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'valor_frete'           => ['sometimes', 'nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'data_previsao_repasse' => ['sometimes', 'nullable', 'date'],
            // 'atrasado' é estado exclusivo do financeiro — não pode ser definido na venda.
            'status'                => ['sometimes', Rule::enum(TransactionStatus::class)->except([TransactionStatus::OVERDUE])],
            'observacao'            => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'string'  => 'O campo :attribute deve ser uma string.',
            'max'     => 'O campo :attribute deve ter no máximo :max caracteres.',
            'unique'  => 'O campo :attribute já está em uso.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'exists'  => 'O campo :attribute deve existir na tabela :table.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'min'     => 'O campo :attribute deve ser maior ou igual a :min.',
            'decimal' => 'O campo :attribute deve ter no máximo 2 casas decimais.',
            'date'    => 'O campo :attribute deve ser uma data válida.',
        ];
    }

    public function attributes(): array
    {
        return [
            //
        ];
    }

    protected function prepareForValidation(): void
    {
        //
    }
}
