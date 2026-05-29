<?php

namespace App\DTOs\Purchases;

use App\Models\Purchases\CompraItem;

class CompraItemResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $compra_id,
        public readonly int $product_id,
        public readonly int $quantidade,
        public readonly float $valor_unitario,
        public readonly float $valor_total,
    ) {}

    public static function fromModel(CompraItem $model): self
    {
        return new self(
            id:             $model->id,
            compra_id:      $model->compra_id,
            product_id:     $model->product_id,
            quantidade:     $model->quantidade,
            valor_unitario: (float) $model->valor_unitario,
            valor_total:    (float) $model->valor_total,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'compra_id'      => $this->compra_id,
            'product_id'     => $this->product_id,
            'quantidade'     => $this->quantidade,
            'valor_unitario' => $this->valor_unitario,
            'valor_total'    => $this->valor_total,
        ];
    }
}
