<?php

namespace App\DTOs\Auth;

use App\Models\Accout\User;

class LoginResponseDTO
{
    public function __construct(
        public readonly string $token,
        public readonly string $name,
        public readonly string $email,
        public readonly ?array $company = null,
        public readonly ?array $role    = null,
    ) {}

    public static function fromToken(string $token, User $user): self
    {
        $companyUser = $user->companyUsers->first();

        return new self(
            token:   $token,
            name:    $user->name,
            email:   $user->email,
            company: $companyUser?->company ? [
                'id'   => $companyUser->company->id,
                'name' => $companyUser->company->name,
            ] : null,
            role: $companyUser?->role ? [
                'id'   => $companyUser->role->id,
                'name' => $companyUser->role->name,
            ] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'token'   => $this->token,
            'name'    => $this->name,
            'email'   => $this->email,
            // 'company' => $this->company,
            // 'role'    => $this->role,
        ];
    }
}
