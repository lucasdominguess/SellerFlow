<?php

namespace App\DTOs\Finance;

use App\Models\Finance\AccountReceivable;

class AccountReceivableResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $company_id,
        public readonly int $store_id,
        public readonly float $valor,
        public readonly ?string $previsao_recebimento,
        public readonly ?string $recebido_em,
        public readonly string $status,
        public readonly string $origem_tipo,
        public readonly ?int $origem_id,
        public readonly ?string $observacao,
    ) {}

    public static function fromModel(AccountReceivable $model): self
    {
        return new self(
            id: $model->id,
            company_id: $model->company_id,
            store_id: $model->store_id,
            valor: (float) $model->valor,
            previsao_recebimento: $model->previsao_recebimento?->toDateString(),
            recebido_em: $model->recebido_em?->toDateString(),
            status: $model->status->value,
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
            'store_id' => $this->store_id,
            'valor' => $this->valor,
            'previsao_recebimento' => $this->previsao_recebimento,
            'recebido_em' => $this->recebido_em,
            'status' => $this->status,
            'origem_tipo' => $this->origem_tipo,
            'origem_id' => $this->origem_id,
            'observacao' => $this->observacao,
        ];
    }
}
