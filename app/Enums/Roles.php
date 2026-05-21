<?php
namespace App\Enums;


enum Roles: int
{
    case ADMIN = 1;
    case USER = 2;


   public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::USER  => 'user',
        };
    }
}
