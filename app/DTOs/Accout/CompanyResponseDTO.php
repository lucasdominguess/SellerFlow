<?php

namespace App\DTOs\Accout;

use App\Models\ListSuspended\Company;

class CompanyResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $cnpj,
        public readonly ?string $description,
        public readonly int $status_id,
    ) {}

    public static function fromModel(Company $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            cnpj: $model->cnpj,
            description: $model->description,
            status_id: $model->status_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'cnpj'        => $this->cnpj,
            'description' => $this->description,
            'status_id'   => $this->status_id,
        ];
    }
}
