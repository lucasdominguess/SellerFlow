<?php

namespace App\DTOs\Accout;

class StoreDTO
{
    public function __construct(
      public readonly string $name,
      public readonly ?string $email,
        public readonly ?string $description,
        public readonly int $status_id,
        public readonly int $marketplace_id,
        public readonly ?int $company_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            description: $data['description'] ?? null,
            status_id: $data['status_id'],
            marketplace_id: $data['marketplace_id'],
            company_id: $data['company_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'marketplace_id' => $this->marketplace_id,
            'company_id' => $this->company_id,
        ];
    }
}
