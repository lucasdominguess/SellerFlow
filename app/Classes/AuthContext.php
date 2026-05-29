<?php

namespace App\Classes;

use Illuminate\Support\Collection;
use Tymon\JWTAuth\Payload;

class AuthContext
{
    // --- Identidade ---
    //retornar todos os dados do usuario
    public static function user(): ?array
    {
        return self::get('user');
    }
    public static function userId(): ?int
    {
        return self::get('user.id');
    }

    public static function userName(): ?string
    {
        return self::get('user.name');
    }

    public static function userEmail(): ?string
    {
        return self::get('user.email');
    }

    public static function userStatus(): ?string
    {
        return self::get('user.status_id');
    }


    // --- Empresa ---
    //retornar os dados das empresas do usuario
    public static function getCompanies(): Collection
    {
        return collect(self::get('company') ?? []);
    }

    public static function companyIds(): Collection
    {
        return self::getCompanies()->pluck('company_id');
    }

    public static function companyName(): ?string
    {
        return self::get('company.company_name');
    }

    public static function roleId(): ?int
    {
        return self::get('company.role_id');
    }

    public static function roleName(): ?string
    {
        return self::get('company.role_name');
    }
    public static function companyStatus(): ?int
    {
        return self::get('company.status_id');
    }

    // --- Lojas ---
    //retornar os dados das lojas do usuario
    public static function stores(): Collection
    {
        return collect(self::get('stores') ?? []);
    }

    public static function storeIds(): Collection
    {
        return self::stores()->pluck('store_id');
    }

    // --- Verificações ---

    public static function hasRole(int|array $roleIds): bool
    {
        return in_array(self::roleId(), (array) $roleIds, true);
    }

    public static function belongsToStore(int $storeId): bool
    {
        return self::storeIds()->contains($storeId);
    }

    public static function check(): bool
    {
        return self::payload() !== null;
    }

    // --- Internos ---

    private static function get(string $key): mixed
    {
        return self::payload()?->get($key);
    }

    // Retorna null quando não há token válido, em vez de lançar exceção.
    private static function payload(): ?Payload
    {
        try {
            return auth('api')->payload();
        } catch (\Throwable) {
            return null;
        }
    }
}
