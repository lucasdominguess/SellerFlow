<?php

namespace Tests\Feature\Repositories;

use App\Models\Accout\User;
use App\Repositories\Accout\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('UserRepository', function () {

    beforeEach(function () {
        // status_id=2 e exigido pela UserFactory como FK para a tabela status
        DB::table('status')->insert(['id' => 2, 'name' => 'Ativo']);

        $this->repository = app(UserRepository::class);
    });

    // verifica que store persiste o usuario no banco e retorna o model criado
    it('grava o usuário no banco e retorna o model', function () {
        $data   = [
            'name'      => 'Usuario Repo',
            'email'     => 'repo@exemplo.com',
            'password'  => bcrypt('Senha@123'),
            'status_id' => 2,
        ];
        $result = $this->repository->store($data);

        expect($result)->toBeInstanceOf(User::class)
            ->and($result->name)->toBe('Usuario Repo');

        $this->assertDatabaseHas('users', ['email' => 'repo@exemplo.com']);
    });

    // verifica que show carrega a relacao status no modelo retornado
    it('carrega a relação status ao exibir o usuário', function () {
        $user   = User::factory()->create();
        $result = $this->repository->show($user);

        expect($result->relationLoaded('status'))->toBeTrue();
    });

    // verifica que findByEmail retorna o usuario correto para um email existente
    it('retorna o usuário correto para um e-mail existente', function () {
        $user   = User::factory()->create(['email' => 'busca@exemplo.com']);
        $result = $this->repository->findByEmail('busca@exemplo.com');

        expect($result)->toBeInstanceOf(User::class)
            ->and($result->id)->toBe($user->id);
    });

    // verifica que findByEmail retorna null quando o email nao esta cadastrado
    it('retorna null para e-mail inexistente', function () {
        $result = $this->repository->findByEmail('naoexiste@exemplo.com');

        expect($result)->toBeNull();
    });

    // verifica que update aplica as alteracoes e o banco reflete os novos valores
    it('atualiza os campos do usuário e persiste as mudanças no banco', function () {
        $user   = User::factory()->create(['name' => 'Nome Antigo']);
        $result = $this->repository->update($user, ['name' => 'Nome Novo']);

        expect($result->name)->toBe('Nome Novo');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Novo']);
    });

    // verifica que delete remove o usuario do banco de dados
    it('remove o usuário do banco', function () {
        $user = User::factory()->create();
        $this->repository->delete($user);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    // verifica que index retorna paginacao com a relacao status carregada em cada item
    it('retorna usuários paginados com a relação status carregada', function () {
        User::factory()->count(3)->create();
        $result = $this->repository->index(15, 1, []);

        expect($result->total())->toBe(3)
            ->and($result->items()[0]->relationLoaded('status'))->toBeTrue();
    });

    // verifica que index filtra usuarios pelo nome usando busca parcial (like)
    it('filtra usuários por nome com correspondência parcial', function () {
        User::factory()->create(['name' => 'Lucas Alves']);
        User::factory()->create(['name' => 'Pedro Santos']);

        $result = $this->repository->index(15, 1, ['name' => 'Lucas']);

        expect($result->total())->toBe(1)
            ->and($result->items()[0]->name)->toBe('Lucas Alves');
    });

    // verifica que index filtra usuarios pelo email usando busca parcial (like)
    it('filtra usuários por e-mail com correspondência parcial', function () {
        User::factory()->create(['email' => 'lucas@dominio.com']);
        User::factory()->create(['email' => 'pedro@outro.com']);

        $result = $this->repository->index(15, 1, ['email' => 'lucas']);

        expect($result->total())->toBe(1)
            ->and($result->items()[0]->email)->toBe('lucas@dominio.com');
    });

    // verifica que index retorna todos os usuarios quando nenhum filtro e informado
    it('retorna todos os usuários quando nenhum filtro é aplicado', function () {
        User::factory()->count(5)->create();

        $result = $this->repository->index(15, 1, []);

        expect($result->total())->toBe(5);
    });
});
