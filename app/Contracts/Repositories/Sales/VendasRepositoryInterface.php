<?php

namespace App\Contracts\Repositories\Sales;

use App\DTOs\Sales\VendaItemDTO;
use App\Models\Sales\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendasRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Venda $venda): Venda;

    public function store(array $data): Venda;

    /** @param VendaItemDTO[] $itens */
    public function storeItens(Venda $venda, array $itens): Venda;

    public function update(Venda $venda, array $data): Venda;

    public function delete(Venda $venda);


}
