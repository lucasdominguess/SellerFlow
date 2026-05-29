<?php

namespace App\Contracts\Repositories\Purchases;

use App\DTOs\Purchases\CompraItemDTO;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompraRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Compra $compra): Compra;

    public function store(array $data): Compra;

    /** @param CompraItemDTO[] $itens */
    public function storeItens(Compra $compra, array $itens): Compra;

    public function update(Compra $compra, array $data): Compra;

    public function delete(Compra $compra);
}
