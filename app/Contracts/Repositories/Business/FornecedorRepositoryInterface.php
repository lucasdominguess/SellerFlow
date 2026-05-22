<?php

namespace App\Contracts\Repositories\Business;

use App\Models\Business\Fornecedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FornecedorRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Fornecedor $fornecedor): Fornecedor;

    public function store(array $data): Fornecedor;

    public function update(Fornecedor $fornecedor, array $data): Fornecedor;

    public function delete(Fornecedor $fornecedor);
}
