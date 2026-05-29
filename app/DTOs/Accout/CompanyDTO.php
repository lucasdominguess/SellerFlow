<?php

namespace App\DTOs\Accout;

use App\Enums\Status;

class CompanyDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $cnpj = null,
        public ?string $description = null,
        public ?int $status_id = null,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            cnpj: $data['cnpj'] ?? null,
            description: $data['description'] ?? null,
            status_id: $data['status_id'] ?? Status::PENDING->value,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'description' => $this->description,
            'status_id' => $this->status_id,
        ], function ($value) {
            return !is_null($value);
        });
    }

}
