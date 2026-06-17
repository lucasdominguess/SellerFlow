<?php

namespace App\Contracts\Repositories\Sales;

use App\DTOs\Sales\VendaItemDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendasRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Sale $venda): Sale;

    public function store(array $data): Sale;

    /** @param VendaItemDTO[] $itens */
    public function storeItens(Sale $venda, array $itens): Sale;

    public function update(Sale $venda, array $data): Sale;

    public function delete(Sale $venda);


}
