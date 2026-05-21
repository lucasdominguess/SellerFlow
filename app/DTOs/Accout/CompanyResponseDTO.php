<?php

namespace App\DTOs\Accout;

use App\Models\Accout\Company;

class CompanyResponseDTO
{
    public function __construct(
        // public readonly int $id,
        // public readonly string $name,
    ) {}

    public static function fromModel(Company $model): self
    {
        return new self(
            // id: $model->id,
            // name: $model->name,
        );
    }

    public function toArray(): array
    {
        return [
            // 'id'   => $this->id,
            // 'name' => $this->name,
        ];
    }
}
