<?php

namespace App\Contracts\Repositories\Accout;


use App\Models\ListSuspended\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Company $company): Company;

    public function store(array $data): Company;

    public function update(Company $company, array $data): Company;

    public function delete(Company $company);
}
