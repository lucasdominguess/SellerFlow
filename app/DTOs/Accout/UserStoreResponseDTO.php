<?php

namespace App\DTOs\Accout;

use App\Models\Accout\UserStore;

class UserStoreResponseDTO
{
    public function __construct(
        // public readonly int $id,
        // public readonly string $name,
    ) {}

    public static function fromModel(UserStore $model): self
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
