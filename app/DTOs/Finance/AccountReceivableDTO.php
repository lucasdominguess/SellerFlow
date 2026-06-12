<?php

namespace App\DTOs\Finance;

class AccountReceivableDTO
{
    public function __construct(
        public readonly ?int $company_id,
        public readonly ?int $store_id,
        public readonly ?float $valor,
        public readonly ?string $previsao_recebimento,
        public readonly ?string $recebido_em,
        public readonly ?string $status,
        public readonly ?string $origem_tipo,
        public readonly ?int $origem_id,
        public readonly ?string $observacao,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            company_id: isset($data['company_id']) ? (int) $data['company_id'] : null,
            store_id: isset($data['store_id']) ? (int) $data['store_id'] : null,
            valor: isset($data['valor']) ? (float) $data['valor'] : null,
            previsao_recebimento: $data['previsao_recebimento'] ?? null,
            recebido_em: $data['recebido_em'] ?? null,
            status: $data['status'] ?? null,
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
            'store_id' => $this->store_id,
            'valor' => $this->valor,
            'previsao_recebimento' => $this->previsao_recebimento,
            'recebido_em' => $this->recebido_em,
            'status' => $this->status,
            'origem_tipo' => $this->origem_tipo,
            'origem_id' => $this->origem_id,
            'observacao' => $this->observacao,
        ], fn($value) => !is_null($value));
    }
}
