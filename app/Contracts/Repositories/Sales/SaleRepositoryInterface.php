<?php

namespace App\Contracts\Repositories\Sales;

use App\DTOs\Sales\SaleItemDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Sale $sale): Sale;

    public function store(array $data): Sale;

    /** @param SaleItemDTO[] $itens */
    public function storeItens(Sale $sale, array $itens): Sale;

    public function update(Sale $sale, array $data): Sale;

    public function delete(Sale $sale);


}
