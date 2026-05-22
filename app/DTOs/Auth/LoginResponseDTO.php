<?php

namespace App\DTOs\Auth;

use App\Models\Accout\User;

class LoginResponseDTO
{
    public function __construct(
        public readonly string $token,
        public readonly string $name,
        public readonly string $email,
    ) {}

    public static function fromToken(string $token, User $user): self
    {
        return new self(
            token: $token,
            name: $user->name,
            email: $user->email,
        );
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }
}
