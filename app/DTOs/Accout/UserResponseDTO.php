<?php

namespace App\DTOs\Accout;

use App\Models\Accout\User;

class UserResponseDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email ,
        public int $status_id,
    ) {}

    public static function fromModel(User $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            status_id: $model->status_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status_id' => $this->status_id,
        ];
    }
}
