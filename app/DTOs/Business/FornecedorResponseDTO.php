<?php

namespace App\DTOs\Business;

use App\Models\Business\Supplier;

class FornecedorResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $responsavel,
        public readonly string $cnpj,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly ?string $link_catalog,
        public readonly ?string $description,
        public readonly int $status_id,
    ) {}

    public static function fromModel(Supplier $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            responsavel: $model->responsavel,
            cnpj: $model->cnpj,
            email: $model->email,
            phone: $model->phone,
            address: $model->address,
            link_catalog: $model->link_catalog,
            description: $model->description,
            status_id: $model->status_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'responsavel'  => $this->responsavel,
            'cnpj'         => $this->cnpj,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'link_catalog' => $this->link_catalog,
            'description'  => $this->description,
            'status_id'    => $this->status_id,
        ];
    }
}
