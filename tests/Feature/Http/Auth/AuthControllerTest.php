<?php

namespace Tests\Feature\Http\Auth;

use App\Models\Accout\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('AuthController', function () {

    beforeEach(function () {
        // status: 1 ativo, 2 inativo, 3 pendente (companyUser do register usa pendente);
        // roles: 1 admin, 2 user (register cria companyUser com role 'user')
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'user'],
        ]);
    });

    // --- POST /api/v1/auth/login ---

    it('logs in an active user and returns a token', function () {
        User::factory()->create([
            'email'     => 'active@example.com',
            'password'  => 'password',
            'status_id' => 1,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'active@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'data' => ['token', 'name', 'email']]);

        expect($response->json('data.token'))->not->toBeEmpty();
        expect($response->headers->get('Authorization'))->toStartWith('Bearer ');
    });

    it('rejects invalid credentials with 401', function () {
        User::factory()->create([
            'email'     => 'active@example.com',
            'password'  => 'password',
            'status_id' => 1,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'active@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401);
    });

    it('blocks login for an inactive user with 403', function () {
        User::factory()->create([
            'email'     => 'inactive@example.com',
            'password'  => 'password',
            'status_id' => 2, // inativo
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password',
        ])->assertStatus(403);
    });

    it('validates required fields on login with 422', function () {
        $this->postJson('/api/v1/auth/login', [])->assertStatus(422);
    });

    // --- POST /api/v1/auth/register ---

    it('registers a user with company and returns 200', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'             => 'Novo Seller',
            'email'            => 'novo@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Senha@123',
            'company_name'     => 'Minha Loja LTDA',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Novo Seller')
            ->assertJsonPath('data.email', 'novo@example.com');

        $this->assertDatabaseHas('users', ['email' => 'novo@example.com']);
        $this->assertDatabaseHas('companies', ['name' => 'Minha Loja LTDA']);
        $this->assertDatabaseHas('company_users', ['company_id' => 1]);
    });

    it('rejects duplicate email on register with 422', function () {
        User::factory()->create(['email' => 'existente@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Outro',
            'email'            => 'existente@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Senha@123',
            'company_name'     => 'Loja',
        ])->assertStatus(422);
    });

    it('rejects mismatched password confirmation on register with 422', function () {
        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Seller',
            'email'            => 'seller@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Outra@123',
            'company_name'     => 'Loja',
        ])->assertStatus(422);
    });

    it('requires company_name on register with 422', function () {
        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Seller',
            'email'            => 'seller@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Senha@123',
        ])->assertStatus(422);
    });

    // --- POST /api/v1/auth/logout ---

    it('logs out an authenticated user', function () {
        actingAsJwt(User::factory()->create(['status_id' => 1]));

        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    });

    // --- POST /api/v1/auth/refresh ---

    it('refreshes the token for an active user', function () {
        actingAsJwt(User::factory()->create(['status_id' => 1]));

        $response = $this->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        expect($response->json('data.token'))->not->toBeEmpty();
    });
});
