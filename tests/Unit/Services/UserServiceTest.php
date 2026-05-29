<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\DTOs\Accout\UserDTO;
use App\DTOs\Accout\UserResponseDTO;
use App\Models\Accout\User;
use App\Services\Accout\UserService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

describe('UserService', function () {

    beforeEach(function () {
        $this->repositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->service        = new UserService($this->repositoryMock);
    });

    // verifica que store delega ao repository com os dados corretos e retorna UserResponseDTO
    it('stores user and returns UserResponseDTO', function () {
        $dto   = UserDTO::fromRequest([
            'name'      => 'Lucas',
            'email'     => 'lucas@exemplo.com',
            'password'  => 'Senha@123',
            'status_id' => 2,
        ]);
        $model = User::factory()->make([
            'id'        => 1,
            'name'      => 'Lucas',
            'email'     => 'lucas@exemplo.com',
            'status_id' => 2,
        ]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('store')
            ->with($dto->toArray())
            ->willReturn($model);

        $result = $this->service->store($dto);

        expect($result)->toBeInstanceOf(UserResponseDTO::class)
            ->and($result->name)->toBe('Lucas')
            ->and($result->email)->toBe('lucas@exemplo.com');
    });

    // verifica que show delega ao repository e retorna UserResponseDTO do usuario correto
    it('returns UserResponseDTO for existing user on show', function () {
        $model = User::factory()->make(['id' => 5, 'name' => 'Existente', 'status_id' => 2]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('show')
            ->with($model)
            ->willReturn($model);

        $result = $this->service->show($model);

        expect($result)->toBeInstanceOf(UserResponseDTO::class)
            ->and($result->id)->toBe(5);
    });

    // verifica que update repassa os dados ao repository e retorna UserResponseDTO com os novos valores
    it('updates user and returns updated UserResponseDTO', function () {
        $dto      = UserDTO::fromRequest(['name' => 'Nome Atualizado']);
        $original = User::factory()->make(['id' => 3, 'name' => 'Antigo', 'status_id' => 2]);
        $updated  = User::factory()->make(['id' => 3, 'name' => 'Nome Atualizado', 'status_id' => 2]);

        $this->repositoryMock
            ->expects($this->once())
            ->method('update')
            ->with($original, $dto->toArray())
            ->willReturn($updated);

        $result = $this->service->update($original, $dto);

        expect($result)->toBeInstanceOf(UserResponseDTO::class)
            ->and($result->name)->toBe('Nome Atualizado');
    });

    // verifica que delete delega a exclusao ao repository uma unica vez
    it('delegates deletion to repository', function () {
        $model = User::factory()->make();

        $this->repositoryMock
            ->expects($this->once())
            ->method('delete')
            ->with($model);

        $this->service->delete($model);
    });

    // verifica que index transforma os itens do paginator em arrays de UserResponseDTO
    it('transforms paginator items into UserResponseDTO arrays on index', function () {
        $model      = User::factory()->make([
            'id'        => 1,
            'name'      => 'Paginado',
            'email'     => 'paginado@exemplo.com',
            'status_id' => 2,
        ]);
        $collection = new Collection([$model]);
        $paginator  = new LengthAwarePaginator($collection, 1, 15, 1);

        $this->repositoryMock
            ->expects($this->once())
            ->method('index')
            ->with(15, 1, [])
            ->willReturn($paginator);

        $result = $this->service->index();
        $items  = $result->items();

        expect($items)->toBeArray()
            ->and($items[0])->toBeArray()
            ->and($items[0]['name'])->toBe('Paginado')
            ->and($items[0]['email'])->toBe('paginado@exemplo.com');
    });
});
