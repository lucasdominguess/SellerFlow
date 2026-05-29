<?php

namespace App\DTOs\Sales;

class VendaItemDTO
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $quantidade,
        public readonly float $valor_unitario,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id:     (int) $data['product_id'],
            quantidade:     (int) $data['quantidade'],
            valor_unitario: (float) $data['valor_unitario'],
        );
    }

    // valor_total não entra aqui: é derivado (quantidade * valor_unitario) e calculado no Repository
    public function toArray(): array
    {
        return [
            'product_id'     => $this->product_id,
            'quantidade'     => $this->quantidade,
            'valor_unitario' => $this->valor_unitario,
        ];
    }
}
