<?php

namespace App\DTOs\Accout;

use App\Models\ListSuspended\MarketPlace;

class MarketplaceResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?float $taxa_percentual,
        public readonly ?float $taxa_fixa,
        public readonly int $status_id,
    ) {}

    public static function fromModel(MarketPlace $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            taxa_percentual: $model->taxa_percentual,
            taxa_fixa: $model->taxa_fixa,
            status_id: $model->status_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'taxa_percentual'   => $this->taxa_percentual,
            'taxa_fixa' => $this->taxa_fixa,
            'status_id'      => $this->status_id,
        ];
    }
}
