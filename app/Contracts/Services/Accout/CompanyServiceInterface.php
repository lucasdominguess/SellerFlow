<?php

namespace App\Contracts\Services\Accout;

use App\DTOs\Accout\CompanyDTO;
use App\DTOs\Accout\CompanyResponseDTO;
use App\Models\ListSuspended\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompanyServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Company $company): CompanyResponseDTO;

    public function store(CompanyDTO $dto): CompanyResponseDTO;

    public function update(Company $company, CompanyDTO $dto): CompanyResponseDTO;

    public function delete(Company $company);
}
