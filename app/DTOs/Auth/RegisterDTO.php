<?php

namespace App\DTOs\AUth;

use App\DTOs\Accout\CompanyDTO;
use App\DTOs\Accout\UserDTO;

class RegisterDTO
{
    public function __construct(
        public UserDTO $user,
        public CompanyDTO $company,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            user: UserDTO::fromRequest([
                'name'     => $data['name'] ?? null,
                'email'    => $data['email'] ?? null,
                'password' => $data['password'] ?? null,
            ]),
            company: CompanyDTO::fromRequest([
                'name'        => $data['company_name'] ?? null,
                'cnpj'        => $data['cnpj'] ?? null,
                'description' => $data['description'] ?? null,
            ]),
        );
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'company' => $this->company->toArray(),
        ];
    }
}
