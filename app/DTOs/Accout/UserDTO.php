<?php

namespace App\DTOs\Accout;

use App\Enums\Status;

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
            status_id: $data['status_id'] ?? Status::PENDING->value,
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
