<?php

namespace App\DTOs\Finance;

use App\Models\Finance\AccountPayable;

class AccountPayableResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $company_id,
        public readonly float $valor,
        public readonly ?string $vencimento,
        public readonly ?string $pago_em,
        public readonly string $status,
        public readonly ?int $categoria_financeira_id,
        public readonly ?int $forma_pagamento_id,
        public readonly string $origem_tipo,
        public readonly ?int $origem_id,
        public readonly ?string $observacao,
    ) {}

    public static function fromModel(AccountPayable $model): self
    {
        return new self(
            id: $model->id,
            company_id: $model->company_id,
            valor: (float) $model->valor,
            vencimento: $model->vencimento?->toDateString(),
            pago_em: $model->pago_em?->toDateString(),
            status: $model->status->value,
            categoria_financeira_id: $model->categoria_financeira_id,
            forma_pagamento_id: $model->forma_pagamento_id,
            origem_tipo: $model->origem_tipo,
            origem_id: $model->origem_id,
            observacao: $model->observacao,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
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
        ];
    }
}
