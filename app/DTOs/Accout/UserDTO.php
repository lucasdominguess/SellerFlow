<?php

namespace App\DTOs\Accout;

class UserDTO
{
    public function __construct(
       public ?string $name = null,
       public ?string $email = null,
       public ?string $password = null,
       public ?int $status_id = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            // sem default: campo ausente fica null e some no toArray (array_filter),
            // preservando o status atual em updates parciais e deixando o default do banco agir no create.
            status_id: $data['status_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        //filtrar para remover campos nulos
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'status_id' => $this->status_id,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
