<?php

namespace App\Contracts\Repositories\Purchases;

use App\DTOs\Purchases\CompraItemDTO;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompraRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Purchase $compra): Purchase;

    public function store(array $data): Purchase;

    /** @param CompraItemDTO[] $itens */
    public function storeItens(Purchase $compra, array $itens): Purchase;

    public function update(Purchase $compra, array $data): Purchase;

    public function delete(Purchase $compra);
}
