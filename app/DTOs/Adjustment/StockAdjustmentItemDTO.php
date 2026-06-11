<?php

namespace App\DTOs\Adjustment;

class StockAdjustmentItemDTO
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $quantidade,
        public readonly string $motivo,
        public readonly ?string $observacao,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            quantidade: (int) $data['quantidade'],
            motivo: $data['motivo'],
            observacao: $data['observacao'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantidade' => $this->quantidade,
            'motivo' => $this->motivo,
            'observacao' => $this->observacao,
        ];
    }
}
