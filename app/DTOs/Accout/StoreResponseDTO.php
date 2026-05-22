<?php

namespace App\DTOs\Accout;

use App\Models\Accout\Store;

class StoreResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $description,
        public readonly int $status_id,
        public readonly MarketplaceResponseDTO $marketplace,
        public readonly ?CompanyResponseDTO $company,
    ) {}

    public static function fromModel(Store $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            description: $model->description,
            status_id: $model->status_id,
            marketplace: MarketplaceResponseDTO::fromModel($model->marketplace),
            company: $model->company ? CompanyResponseDTO::fromModel($model->company) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'description' => $this->description,
            'status_id'   => $this->status_id,
            'marketplace' => $this->marketplace->toArray(),
            'company'     => $this->company?->toArray(),
        ];
    }
}
