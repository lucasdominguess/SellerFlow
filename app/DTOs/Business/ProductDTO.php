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
        // UploadedFile[] — arquivos a anexar; não vão no toArray() (tabela separada)
        public readonly array $images = [],
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
            images: $data['images'] ?? [],
        );
    }

    public function hasImages(): bool
    {
        return $this->images !== [];
    }

    // Retorna apenas os campos presentes (não-null) da tabela products; images é tratado à parte
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
        ], fn($value) => !is_null($value));
    }
}
