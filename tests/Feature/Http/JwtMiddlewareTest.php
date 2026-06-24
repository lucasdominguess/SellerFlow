<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('JwtMiddleware', function () {

    it('rejeita requisição sem token com 401', function () {
        // /user está atrás do JwtMiddleware; sem Authorization, barra antes do controller
        $this->getJson('/api/v1/user')->assertStatus(401);
    });

    it('rejeita requisição com token malformado com 401', function () {
        $this->withHeader('Authorization', 'Bearer token-invalido-xyz')
            ->getJson('/api/v1/user')
            ->assertStatus(401);
    });
});
