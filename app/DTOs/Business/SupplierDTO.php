<?php

namespace App\DTOs\Business;

class SupplierDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $responsavel,
        public readonly ?string $cnpj,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly ?string $link_catalog,
        public readonly ?string $description,
        public readonly ?int $status_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            responsavel: $data['responsavel'] ?? null,
            cnpj: $data['cnpj'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            link_catalog: $data['link_catalog'] ?? null,
            description: $data['description'] ?? null,
            status_id: $data['status_id'] ?? null,
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update
    public function toArray(): array
    {
        return array_filter([
            'name'         => $this->name,
            'responsavel'  => $this->responsavel,
            'cnpj'         => $this->cnpj,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'link_catalog' => $this->link_catalog,
            'description'  => $this->description,
            'status_id'    => $this->status_id,
        ], fn($value) => !is_null($value));
    }
}
