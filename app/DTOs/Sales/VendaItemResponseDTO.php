<?php

namespace App\DTOs\Sales;

use App\Models\Sales\VendaItem;

class VendaItemResponseDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly int $venda_id,
        public readonly int $product_id,
        public readonly int $quantidade,
        public readonly float $valor_unitario,
        public readonly float $valor_total,
    ) {}

    public static function fromModel(VendaItem $model): self
    {
        return new self(
            id:             $model->id,
            venda_id:       $model->venda_id,
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
            'venda_id'       => $this->venda_id,
            'product_id'     => $this->product_id,
            'quantidade'     => $this->quantidade,
            'valor_unitario' => $this->valor_unitario,
            'valor_total'    => $this->valor_total,
        ];
    }
}
