<?php

namespace App\Http\Requests\ListSuspended;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FilterListSuspendedRequest extends FormRequest
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
                'params' => ['required', 'string', 'in:categoria-financeira,fornecedor,forma-pagamento,marketplace,produto,company'],
                'status_id'=> ['nullable', 'integer', 'exists:status,id'],
                'name' => ['nullable', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'params.required' => 'O campo params é obrigatório.',
            'params.string' => 'O campo params deve ser uma string.',
            'params.in' => 'O campo params deve ser um dos seguintes valores: categoria-financeira, fornecedor, forma-pagamento, marketplace, produto, company.',
            'status_id.integer' => 'O campo status_id deve ser um número inteiro.',
            'status_id.exists' => 'O campo status_id deve existir na tabela de status.',
            'name.string' => 'O campo name deve ser uma string.',
        ];
    }
    public function attributes(): array
    {
        return [
            'params' => 'parâmetro',
            'status_id' => 'status',
            'name' => 'nome',
        ];
    }
    public function prepareForValidation(): void
    {
        //adicionar ao array filters somente os nao nulos
        $filters = [];
        if ($this->has('status_id') && !is_null($this->input('status_id'))) {
            $filters['status_id'] = $this->input('status_id');
        }
        if ($this->has('name') && !is_null($this->input('name'))) {
            $filters['name'] = $this->input('name');
        }
        $this->merge(['filters' => $filters]);
    }
}
