<?php

namespace App\DTOs\Accout;

class UserStoreDTO
{
    public function __construct(
        public readonly string $user_id,
        public readonly string $store_id,
        public readonly string $role_id,
        public readonly string $status_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            user_id: $data['user_id'] ?? null,
            store_id: $data['store_id'] ?? null,
            role_id: $data['role_id'] ?? null,
            status_id: $data['status_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'store_id' => $this->store_id,
            'role_id' => $this->role_id,
            'status_id' => $this->status_id,
        ];
    }
}
