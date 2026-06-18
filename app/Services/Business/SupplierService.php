<?php

namespace App\Services\Business;

use App\Contracts\Repositories\Business\SupplierRepositoryInterface;
use App\Contracts\Services\Business\SupplierServiceInterface;
use App\DTOs\Business\SupplierDTO;
use App\DTOs\Business\SupplierResponseDTO;
use App\Models\Business\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService implements SupplierServiceInterface
{
    public function __construct(
        private SupplierRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return SupplierResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Supplier $supplier): SupplierResponseDTO
    {
        $supplier = $this->repository->show($supplier);

        return SupplierResponseDTO::fromModel($supplier);
    }

    public function store(SupplierDTO $dto): SupplierResponseDTO
    {
        $supplier = $this->repository->store($dto->toArray());

        return SupplierResponseDTO::fromModel($supplier);
    }

    public function update(Supplier $supplier, SupplierDTO $dto): SupplierResponseDTO
    {
        $supplier = $this->repository->update($supplier, $dto->toArray());

        return SupplierResponseDTO::fromModel($supplier);
    }

    public function delete(Supplier $supplier)
    {
        return $this->repository->delete($supplier);
    }
}
