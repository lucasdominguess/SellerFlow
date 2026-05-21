<?php

namespace App\Enums;

enum Status: int
{
    case ACTIVE = 1;

    case INACTIVE = 2;
    case PENDING = 3;


    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'active',
            self::INACTIVE => 'inactive',
            self::PENDING => 'pending',

        };
    }
}
