<?php

namespace App\DTOs\Accout;

class UserDTO
{
    public function __construct(
        // public readonly string $exemplo,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            // exemplo: $data['exemplo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            // 'exemplo' => $this->exemplo,
        ];
    }
}
