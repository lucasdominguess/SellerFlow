<?php

namespace App\DTOs\Business;

class ValidateProductDTO
{
    public function __construct(
    public ?string $name,
    public ?string $brand,
    public ?string $description,
    public ?string $catalog_link,
    public ?int $fornecedor_id,

    public float $price_sale,
    public float $price_buy,
    public float $cust_additional,
    public int $marketplace_id,
    public ?int $user_id,
    public ?int $company_id

    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'] ?? null,
            $data['brand'] ?? null,
            $data['description'] ?? null,
            $data['catalog_link'] ?? null,
            $data['fornecedor_id'] ?? null,
            $data['price_sale'],
            $data['price_buy'],
            $data['cust_additional'] ?? 0,
            $data['marketplace_id'],
            $data['user_id'] ?? null,
            $data['company_id'] ?? null

        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'brand' => $this->brand,
            'description' => $this->description,
            'catalog_link' => $this->catalog_link,
            'fornecedor_id' => $this->fornecedor_id,

            'price_sale' => $this->price_sale,
            'price_buy' => $this->price_buy,
            'cust_additional' => $this->cust_additional,
            'marketplace_id' => $this->marketplace_id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id
        ];
    }
}
