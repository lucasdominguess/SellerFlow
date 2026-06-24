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

    it('autentica um usuário ativo e retorna um token', function () {
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

    it('rejeita credenciais inválidas com 401', function () {
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

    it('bloqueia o login de um usuário inativo com 403', function () {
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

    it('valida os campos obrigatórios no login com 422', function () {
        $this->postJson('/api/v1/auth/login', [])->assertStatus(422);
    });

    // --- POST /api/v1/auth/register ---

    it('registra um usuário com empresa e retorna 200', function () {
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
        // vínculo user↔empresa criado (sem assumir o id da sequence)
        $this->assertDatabaseCount('company_users', 1);
    });

    it('rejeita e-mail duplicado no registro com 422', function () {
        User::factory()->create(['email' => 'existente@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Outro',
            'email'            => 'existente@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Senha@123',
            'company_name'     => 'Loja',
        ])->assertStatus(422);
    });

    it('rejeita confirmação de senha divergente no registro com 422', function () {
        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Seller',
            'email'            => 'seller@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Outra@123',
            'company_name'     => 'Loja',
        ])->assertStatus(422);
    });

    it('exige company_name no registro com 422', function () {
        $this->postJson('/api/v1/auth/register', [
            'name'             => 'Seller',
            'email'            => 'seller@example.com',
            'password'         => 'Senha@123',
            'confirm_password' => 'Senha@123',
        ])->assertStatus(422);
    });

    // --- POST /api/v1/auth/logout ---

    it('desloga um usuário autenticado', function () {
        actingAsJwt(User::factory()->create(['status_id' => 1]));

        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    });

    // --- POST /api/v1/auth/refresh ---

    it('renova o token de um usuário ativo', function () {
        actingAsJwt(User::factory()->create(['status_id' => 1]));

        $response = $this->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        expect($response->json('data.token'))->not->toBeEmpty();
    });

    it('bloqueia o refresh de um usuário que ficou inativo com 403', function () {
        $user = User::factory()->create(['status_id' => 1]);
        actingAsJwt($user);

        // usuário é desativado após autenticar; o refresh revalida o status
        $user->update(['status_id' => 2]);

        $this->postJson('/api/v1/auth/refresh')->assertStatus(403);
    });
});
