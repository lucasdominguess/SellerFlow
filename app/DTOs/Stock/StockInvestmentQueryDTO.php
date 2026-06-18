<?php

namespace App\DTOs\Stock;

class StockInvestmentQueryDTO
{
    public function __construct(
        public readonly int $company_id,
        public readonly ?int $product_id,
        public readonly ?string $product_name,
        public readonly ?string $sku,
        public readonly int $perPage,
        public readonly int $page,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            company_id: $data['company_id'],
            product_id: $data['product_id'] ?? null,
            product_name: $data['product_name'] ?? null,
            sku: $data['sku'] ?? null,
            perPage: $data['perPage'] ?? 15,
            page: $data['page'] ?? 1,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id'   => $this->company_id,
            'product_id'   => $this->product_id,
            'product_name' => $this->product_name,
            'sku'          => $this->sku,
            'perPage'      => $this->perPage,
            'page'         => $this->page,
        ];
    }
}
