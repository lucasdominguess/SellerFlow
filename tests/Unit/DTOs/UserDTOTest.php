<?php

namespace Tests\Unit\DTOs;

use App\DTOs\Accout\UserDTO;
use App\DTOs\Accout\UserResponseDTO;
use App\Models\Accout\User;

describe('UserDTO', function () {

    // verifica que todos os campos sao mapeados corretamente a partir do payload validado
    it('maps all fields from validated array', function () {
        $dto = UserDTO::fromRequest([
            'name'      => 'Lucas Silva',
            'email'     => 'lucas@exemplo.com',
            'password'  => 'Senha@123',
            'status_id' => 2,
        ]);

        expect($dto->name)->toBe('Lucas Silva')
            ->and($dto->email)->toBe('lucas@exemplo.com')
            ->and($dto->password)->toBe('Senha@123')
            ->and($dto->status_id)->toBe(2);
    });

    // verifica que campos ausentes no payload resultam em null no DTO
    it('sets missing fields as null', function () {
        $dto = UserDTO::fromRequest(['name' => 'Apenas Nome']);

        expect($dto->email)->toBeNull()
            ->and($dto->password)->toBeNull()
            ->and($dto->status_id)->toBeNull();
    });

    // verifica que toArray remove campos nulos permitindo update parcial
    it('filters null fields in toArray', function () {
        $dto   = UserDTO::fromRequest(['name' => 'Lucas']);
        $array = $dto->toArray();

        expect($array)->toHaveKey('name')
            ->and($array)->not->toHaveKey('email')
            ->and($array)->not->toHaveKey('password')
            ->and($array)->not->toHaveKey('status_id');
    });

    // verifica que toArray retorna todos os campos quando nenhum deles e nulo
    it('returns all fields in toArray when none are null', function () {
        $dto = UserDTO::fromRequest([
            'name'      => 'Lucas',
            'email'     => 'lucas@exemplo.com',
            'password'  => 'Senha@123',
            'status_id' => 2,
        ]);

        expect($dto->toArray())->toMatchArray([
            'name'      => 'Lucas',
            'email'     => 'lucas@exemplo.com',
            'password'  => 'Senha@123',
            'status_id' => 2,
        ]);
    });
});

describe('UserResponseDTO', function () {

    // verifica que fromModel projeta corretamente os campos publicos do modelo User
    it('projects correct fields from User model', function () {
        $user = User::factory()->make([
            'id'        => 1,
            'name'      => 'Lucas Silva',
            'email'     => 'lucas@exemplo.com',
            'status_id' => 2,
        ]);

        $dto = UserResponseDTO::fromModel($user);

        expect($dto->id)->toBe(1)
            ->and($dto->name)->toBe('Lucas Silva')
            ->and($dto->email)->toBe('lucas@exemplo.com')
            ->and($dto->status_id)->toBe(2);
    });

    // verifica que toArray retorna todas as chaves publicas do contrato da API
    it('returns all public keys in toArray', function () {
        $user = User::factory()->make(['id' => 1, 'status_id' => 2]);
        $dto  = UserResponseDTO::fromModel($user);

        expect($dto->toArray())
            ->toHaveKey('id')
            ->toHaveKey('name')
            ->toHaveKey('email')
            ->toHaveKey('status_id');
    });

    // verifica que o campo password nao aparece no DTO de resposta
    it('does not expose password field', function () {
        $user = User::factory()->make(['id' => 1, 'status_id' => 2]);
        $dto  = UserResponseDTO::fromModel($user);

        expect($dto->toArray())->not->toHaveKey('password');
    });
});
