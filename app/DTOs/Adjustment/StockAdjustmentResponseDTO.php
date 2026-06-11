<?php

namespace App\DTOs\Adjustment;

use App\Models\Stock\StockAdjustment;

class StockAdjustmentResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $company_id,
        public readonly int $product_id,
        public readonly ?int $user_id,
        public readonly int $quantidade,
        public readonly string $motivo,
        public readonly ?string $observacao,
    ) {
    }

    public static function fromModel(StockAdjustment $model): self
    {
        return new self(
            id: $model->id,
            company_id: $model->company_id,
            product_id: $model->product_id,
            user_id: $model->user_id,
            quantidade: $model->quantidade,
            motivo: $model->motivo,
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
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'quantidade' => $this->quantidade,
            'motivo' => $this->motivo,
            'observacao' => $this->observacao,
        ];
    }
}
