<?php

namespace App\Services\Accout;

use App\Contracts\Repositories\Accout\StoreRepositoryInterface;
use App\Contracts\Services\Accout\StoreServiceInterface;
use App\DTOs\Accout\StoreDTO;
use App\DTOs\Accout\StoreResponseDTO;
use App\Models\Accout\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreService implements StoreServiceInterface
{
    public function __construct(
        private StoreRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return StoreResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Store $store): StoreResponseDTO
    {
        $store = $this->repository->show($store);

        return StoreResponseDTO::fromModel($store);
    }

    public function store(StoreDTO $dto): StoreResponseDTO
    {
        $store = $this->repository->store($dto->toArray());

        return StoreResponseDTO::fromModel($store);
    }

    public function update(Store $store, StoreDTO $dto): StoreResponseDTO
    {
        $store = $this->repository->update($store, $dto->toArray());

        return StoreResponseDTO::fromModel($store);
    }

    public function delete(Store $store)
    {
        return $this->repository->delete($store);
    }
}
