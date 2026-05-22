<?php

namespace App\DTOs\Business;

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
        public readonly ?string $path_image,
        public readonly ?FornecedorResponseDTO $fornecedor,
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
            path_image: $model->path_image,
            fornecedor: $model->fornecedor
                ? FornecedorResponseDTO::fromModel($model->fornecedor)
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
            'path_image'  => $this->path_image,
            'fornecedor'  => $this->fornecedor?->toArray(),
        ];
    }
}
