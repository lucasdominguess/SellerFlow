<?php

namespace App\Services\Accout;

use App\Contracts\Repositories\Accout\CompanyRepositoryInterface;
use App\Contracts\Services\Accout\CompanyServiceInterface;
use App\DTOs\Accout\CompanyDTO;
use App\DTOs\Accout\CompanyResponseDTO;
use App\Models\Accout\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService implements CompanyServiceInterface
{
    public function __construct(
        private CompanyRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return CompanyResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Company $company): CompanyResponseDTO
    {
        $company = $this->repository->show($company);

        return CompanyResponseDTO::fromModel($company);
    }

    public function store(CompanyDTO $dto): CompanyResponseDTO
    {
        $company = $this->repository->store($dto->toArray());

        return CompanyResponseDTO::fromModel($company);
    }

    public function update(Company $company, CompanyDTO $dto): CompanyResponseDTO
    {
        $company = $this->repository->update($company, $dto->toArray());

        return CompanyResponseDTO::fromModel($company);
    }

    public function delete(Company $company)
    {
        return $this->repository->delete($company);
    }
}
