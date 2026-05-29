<?php

namespace App\DTOs\Auth;

use App\Models\Accout\User;

class RegisterResponseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int    $status_id,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            name:      $user->name,
            email:     $user->email,
            status_id: $user->status_id,
        );
    }

    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'email'     => $this->email,
            'status_id' => $this->status_id,
        ];
    }
}
