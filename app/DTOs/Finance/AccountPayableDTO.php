<?php

namespace App\DTOs\Finance;

class AccountPayableDTO
{
    public function __construct(
        public readonly ?int $company_id,
        public readonly ?float $valor,
        public readonly ?string $vencimento,
        public readonly ?string $pago_em,
        public readonly ?string $status,
        public readonly ?int $categoria_financeira_id,
        public readonly ?int $forma_pagamento_id,
        public readonly ?string $origem_tipo,
        public readonly ?int $origem_id,
        public readonly ?string $observacao,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            company_id: isset($data['company_id']) ? (int) $data['company_id'] : null,
            valor: isset($data['valor']) ? (float) $data['valor'] : null,
            vencimento: $data['vencimento'] ?? null,
            pago_em: $data['pago_em'] ?? null,
            status: $data['status'] ?? null,
            categoria_financeira_id: isset($data['categoria_financeira_id']) ? (int) $data['categoria_financeira_id'] : null,
            forma_pagamento_id: isset($data['forma_pagamento_id']) ? (int) $data['forma_pagamento_id'] : null,
            origem_tipo: $data['origem_tipo'] ?? null,
            origem_id: isset($data['origem_id']) ? (int) $data['origem_id'] : null,
            observacao: $data['observacao'] ?? null,
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update
    public function toArray(): array
    {
        return array_filter([
            'company_id' => $this->company_id,
            'valor' => $this->valor,
            'vencimento' => $this->vencimento,
            'pago_em' => $this->pago_em,
            'status' => $this->status,
            'categoria_financeira_id' => $this->categoria_financeira_id,
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'origem_tipo' => $this->origem_tipo,
            'origem_id' => $this->origem_id,
            'observacao' => $this->observacao,
        ], fn($value) => !is_null($value));
    }
}
