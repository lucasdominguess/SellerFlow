<?php

namespace App\DTOs\Accout;

class UserStoreDTO
{
    public function __construct(
        public readonly ?int $user_id,
        public readonly ?int $store_id,
        public readonly ?int $role_id,
        public readonly ?int $status_id,
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

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update.
    // No create, todos os 4 campos são obrigatórios na validação, então sempre vêm preenchidos.
    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->user_id,
            'store_id' => $this->store_id,
            'role_id' => $this->role_id,
            'status_id' => $this->status_id,
        ], fn ($value) => !is_null($value));
    }
}
