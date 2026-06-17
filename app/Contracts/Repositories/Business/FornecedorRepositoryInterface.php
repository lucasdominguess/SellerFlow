<?php

namespace App\Contracts\Repositories\Business;

use App\Models\Business\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FornecedorRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Supplier $fornecedor): Supplier;

    public function store(array $data): Supplier;

    public function update(Supplier $fornecedor, array $data): Supplier;

    public function delete(Supplier $fornecedor);
}
