<?php

namespace App\DTOs\Business;

use App\DTOs\Business\SupplierResponseDTO;
use App\Models\Business\Product;

class ProductResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $sku,
        public readonly string $name,
        public readonly ?string $marca,
        public readonly ?string $description,
        public readonly float $price_unit,
        public readonly float $price_box,
        public readonly int $status_id,
        public readonly array $images,
        public readonly ?SupplierResponseDTO $fornecedor,
    ) {}

    public static function fromModel(Product $model): self
    {
        return new self(
            id: $model->id,
            sku: $model->sku,
            name: $model->name,
            marca: $model->marca,
            description: $model->description,
            price_unit: $model->price_unit,
            price_box: $model->price_box,
            status_id: $model->status_id,
            images: $model->images
                ->map(fn($image) => ProductImageResponseDTO::fromModel($image)->toArray())
                ->values()
                ->all(),
            fornecedor: $model->supplier
                ? SupplierResponseDTO::fromModel($model->supplier)
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'sku'         => $this->sku,
            'name'        => $this->name,
            'marca'       => $this->marca,
            'description' => $this->description,
            'price_unit'  => $this->price_unit,
            'price_box'   => $this->price_box,
            'status_id'   => $this->status_id,
            'images'      => $this->images,
            'fornecedor'  => $this->fornecedor?->toArray(),
        ];
    }
}
