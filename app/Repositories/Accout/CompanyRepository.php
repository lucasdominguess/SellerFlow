<?php

namespace App\Repositories\Accout;

use App\Contracts\Repositories\Accout\CompanyRepositoryInterface;
use App\Models\Accout\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(
        private Company $companyModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->companyModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Company $company): Company
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $company;
    }

    public function store(array $data): Company
    {
        return $this->companyModel->create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company;
    }

    public function delete(Company $company)
    {
        return $company->delete();
    }
}
