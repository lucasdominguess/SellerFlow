<?php

namespace App\DTOs\Accout;

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
            // sem default: campo ausente fica null e some no toArray (array_filter),
            // preservando o status atual em updates parciais; o create usa o default do model.
            status_id: $data['status_id'] ?? null,
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
