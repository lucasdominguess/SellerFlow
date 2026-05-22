<?php

namespace App\Services\Business;

use App\Contracts\Repositories\Business\FornecedorRepositoryInterface;
use App\Contracts\Services\Business\FornecedorServiceInterface;
use App\DTOs\Business\FornecedorDTO;
use App\DTOs\Business\FornecedorResponseDTO;
use App\Models\Business\Fornecedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FornecedorService implements FornecedorServiceInterface
{
    public function __construct(
        private FornecedorRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return FornecedorResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Fornecedor $fornecedor): FornecedorResponseDTO
    {
        $fornecedor = $this->repository->show($fornecedor);

        return FornecedorResponseDTO::fromModel($fornecedor);
    }

    public function store(FornecedorDTO $dto): FornecedorResponseDTO
    {
        $fornecedor = $this->repository->store($dto->toArray());

        return FornecedorResponseDTO::fromModel($fornecedor);
    }

    public function update(Fornecedor $fornecedor, FornecedorDTO $dto): FornecedorResponseDTO
    {
        $fornecedor = $this->repository->update($fornecedor, $dto->toArray());

        return FornecedorResponseDTO::fromModel($fornecedor);
    }

    public function delete(Fornecedor $fornecedor)
    {
        return $this->repository->delete($fornecedor);
    }
}
