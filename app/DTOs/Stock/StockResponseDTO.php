<?php

namespace App\DTOs\Stock;

use App\Models\Stock\Stock;

class StockResponseDTO implements \JsonSerializable
{
    public function __construct(
        // public readonly int $id,
        // public readonly string $name,
    ) {}

    public static function fromModel(Stock $model): self
    {
        return new self(
            // id: $model->id,
            // name: $model->name,
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            // 'id'   => $this->id,
            // 'name' => $this->name,
        ];
    }
}
