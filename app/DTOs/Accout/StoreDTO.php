<?php

namespace App\DTOs\Accout;

class StoreDTO
{
    public function __construct(
      public readonly ?string $name,
      public readonly ?string $email,
        public readonly ?string $description,
        public readonly ?int $status_id,
        public readonly ?int $marketplace_id,
        public readonly ?int $company_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            description: $data['description'] ?? null,
            status_id: $data['status_id'] ?? null,
            marketplace_id: $data['marketplace_id'] ?? null,
            company_id: $data['company_id'] ?? null,
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update.
    // No create, name/status_id/marketplace_id são obrigatórios na validação, então sempre vêm preenchidos.
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'marketplace_id' => $this->marketplace_id,
            'company_id' => $this->company_id,
        ], fn ($value) => !is_null($value));
    }
}
