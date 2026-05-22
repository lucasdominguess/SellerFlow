<?php

namespace App\DTOs\Business;

class ProductDTO
{
    public function __construct(
        public readonly ?string $sku,
        public readonly ?string $name,
        public readonly ?string $marca,
        public readonly ?string $description,
        public readonly ?float $price_unit,
        public readonly ?float $price_box,
        public readonly ?int $status_id,
        public readonly ?int $fornecedor_id,
        public readonly ?string $path_image,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            sku: $data['sku'] ?? null,
            name: $data['name'] ?? null,
            marca: $data['marca'] ?? null,
            description: $data['description'] ?? null,
            price_unit: isset($data['price_unit']) ? (float) $data['price_unit'] : null,
            price_box: isset($data['price_box']) ? (float) $data['price_box'] : null,
            status_id: $data['status_id'] ?? null,
            fornecedor_id: $data['fornecedor_id'] ?? null,
            path_image: $data['path_image'] ?? null,
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update
    public function toArray(): array
    {
        return array_filter([
            'sku'           => $this->sku,
            'name'          => $this->name,
            'marca'         => $this->marca,
            'description'   => $this->description,
            'price_unit'    => $this->price_unit,
            'price_box'     => $this->price_box,
            'status_id'     => $this->status_id,
            'fornecedor_id' => $this->fornecedor_id,
            'path_image'    => $this->path_image,
        ], fn($value) => !is_null($value));
    }
}
