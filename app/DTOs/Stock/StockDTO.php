<?php

namespace App\DTOs\Stock;

class StockDTO
{
    public function __construct(
      public int $product_id,
      public int $quantidade,
      public string $tipo,
      public string $origem_tipo,
      public int $origem_id,
      public ?string $observacao,
      public ?int $user_id,
      public ?int $company_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            quantidade: $data['quantidade'],
            tipo: $data['tipo'],
            origem_tipo: $data['origem_tipo'],
            origem_id: $data['origem_id'],
            observacao: $data['observacao'] ?? null,
            user_id: $data['user_id'] ?? null,
            company_id: $data['company_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantidade' => $this->quantidade,
            'tipo' => $this->tipo,
            'origem_tipo' => $this->origem_tipo,
            'origem_id' => $this->origem_id,
            'observacao' => $this->observacao,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
        ];
    }
}
