<?php

namespace App\Services\Accout;

use App\Contracts\Repositories\Accout\UserStoreRepositoryInterface;
use App\Contracts\Services\Accout\UserStoreServiceInterface;
use App\DTOs\Accout\UserStoreDTO;
use App\DTOs\Accout\UserStoreResponseDTO;
use App\Models\Accout\UserStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserStoreService implements UserStoreServiceInterface
{
    public function __construct(
        private UserStoreRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return UserStoreResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(UserStore $userStore): UserStoreResponseDTO
    {
        $userStore = $this->repository->show($userStore);

        return UserStoreResponseDTO::fromModel($userStore);
    }

    public function store(UserStoreDTO $dto): UserStoreResponseDTO
    {
        $userStore = $this->repository->store($dto->toArray());

        return UserStoreResponseDTO::fromModel($userStore);
    }

    public function update(UserStore $userStore, UserStoreDTO $dto): UserStoreResponseDTO
    {
        $userStore = $this->repository->update($userStore, $dto->toArray());

        return UserStoreResponseDTO::fromModel($userStore);
    }

    public function delete(UserStore $userStore)
    {
        return $this->repository->delete($userStore);
    }
}
