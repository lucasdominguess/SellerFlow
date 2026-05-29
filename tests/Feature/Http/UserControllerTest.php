<?php

namespace Tests\Feature\Http;

use App\Models\Accout\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('UserController', function () {

    beforeEach(function () {
        // status_id=2 e exigido pela UserFactory como FK para a tabela status
        DB::table('status')->insert(['id' => 2, 'name' => 'Ativo']);
    });

    // --- GET /api/v1/user ---

    // verifica que a listagem retorna status 200 com envelope de paginacao correto
    it('lists users with pagination meta', function () {
        User::factory()->count(2)->create();

        $this->getJson('/api/v1/user')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data',
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ]);
    });

    // verifica que perPage e page da query string sao respeitados na paginacao
    it('respects perPage and page query params', function () {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/user?perPage=2&page=1');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5);
    });

    // --- GET /api/v1/user/{user} ---

    // verifica que show retorna os dados corretos do usuario solicitado
    it('returns correct user data on show', function () {
        $user = User::factory()->create(['name' => 'Lucas Detalhado']);

        $this->getJson("/api/v1/user/{$user->id}")
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Lucas Detalhado')
            ->assertJsonPath('data.email', $user->email);
    });

    // verifica que show retorna 404 para um ID que nao existe no banco
    it('returns 404 for nonexistent user on show', function () {
        $this->getJson('/api/v1/user/99999')
            ->assertStatus(404);
    });

    // verifica que o campo password nao aparece na resposta do show
    it('does not expose password in show response', function () {
        $user = User::factory()->create();

        $this->getJson("/api/v1/user/{$user->id}")
            ->assertStatus(200)
            ->assertJsonMissingPath('data.password');
    });

    // --- POST /api/v1/user ---

    // verifica que store cria o usuario e retorna 201 com os dados no envelope
    it('creates user and returns 201 with data', function () {
        $response = $this->postJson('/api/v1/user', [
            'name'               => 'Novo Usuario',
            'email'              => 'novo@exemplo.com',
            'password'           => 'Senha@123',
            'confirmed_password' => 'Senha@123',
            'status_id'          => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Novo Usuario')
            ->assertJsonPath('data.email', 'novo@exemplo.com');

        $this->assertDatabaseHas('users', ['email' => 'novo@exemplo.com']);
    });

    // verifica que store retorna 422 quando o campo name esta ausente
    it('returns 422 when name is missing on store', function () {
        $this->postJson('/api/v1/user', [
            'email'              => 'teste@exemplo.com',
            'password'           => 'Senha@123',
            'confirmed_password' => 'Senha@123',
            'status_id'          => 2,
        ])->assertStatus(422);
    });

    // verifica que store retorna 422 quando o email ja esta cadastrado
    it('returns 422 when email is already taken on store', function () {
        User::factory()->create(['email' => 'existente@exemplo.com']);

        $this->postJson('/api/v1/user', [
            'name'               => 'Outro Usuario',
            'email'              => 'existente@exemplo.com',
            'password'           => 'Senha@123',
            'confirmed_password' => 'Senha@123',
            'status_id'          => 2,
        ])->assertStatus(422);
    });

    // verifica que store retorna 422 quando as senhas nao coincidem
    it('returns 422 when passwords do not match on store', function () {
        $this->postJson('/api/v1/user', [
            'name'               => 'Usuario Teste',
            'email'              => 'teste@exemplo.com',
            'password'           => 'Senha@123',
            'confirmed_password' => 'SenhaErrada@123',
            'status_id'          => 2,
        ])->assertStatus(422);
    });

    // verifica que store retorna 422 quando a senha tem menos de 8 caracteres
    it('returns 422 when password is shorter than 8 characters', function () {
        $this->postJson('/api/v1/user', [
            'name'               => 'Usuario Teste',
            'email'              => 'teste@exemplo.com',
            'password'           => '123',
            'confirmed_password' => '123',
            'status_id'          => 2,
        ])->assertStatus(422);
    });

    // --- PUT /api/v1/user/{user} ---

    // verifica que update altera o nome do usuario e retorna os novos dados
    it('updates user name and returns updated data', function () {
        $user = User::factory()->create(['name' => 'Nome Antigo']);

        $this->putJson("/api/v1/user/{$user->id}", ['name' => 'Nome Atualizado'])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Nome Atualizado');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Atualizado']);
    });

    // verifica que update retorna 422 quando o email ja pertence a outro usuario
    it('returns 422 when email belongs to another user on update', function () {
        $userA = User::factory()->create(['email' => 'a@exemplo.com']);
        $userB = User::factory()->create(['email' => 'b@exemplo.com']);

        $this->putJson("/api/v1/user/{$userB->id}", ['email' => 'a@exemplo.com'])
            ->assertStatus(422);
    });

    // verifica que update permite reutilizar o proprio email do usuario sem conflito
    it('allows user to keep own email on update', function () {
        $user = User::factory()->create(['email' => 'proprio@exemplo.com']);

        $this->putJson("/api/v1/user/{$user->id}", [
            'email' => 'proprio@exemplo.com',
            'name'  => 'Nome Novo',
        ])->assertStatus(200);
    });

    // verifica que update retorna 404 para um ID que nao existe
    it('returns 404 when updating nonexistent user', function () {
        $this->putJson('/api/v1/user/99999', ['name' => 'Qualquer'])
            ->assertStatus(404);
    });

    // --- DELETE /api/v1/user/{user} ---

    // verifica que delete remove o usuario do banco e retorna sucesso
    it('deletes user and returns success response', function () {
        $user = User::factory()->create();

        $this->deleteJson("/api/v1/user/{$user->id}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    // verifica que delete retorna 404 para um ID que nao existe
    it('returns 404 when deleting nonexistent user', function () {
        $this->deleteJson('/api/v1/user/99999')
            ->assertStatus(404);
    });
});
