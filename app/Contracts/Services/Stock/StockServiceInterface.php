<?php

namespace App\Contracts\Services\Stock;

use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Sales\VendasDTO;
use App\DTOs\Stock\StockDTO;
use App\DTOs\Stock\StockResponseDTO;
use App\Models\Stock\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Stock $stock): StockResponseDTO;

    public function store(StockDTO $dto): StockResponseDTO;

    public function update(Stock $stock, StockDTO $dto): StockResponseDTO;

    public function delete(Stock $stock);

    public function proccessItensPurchase(CompraDTO $compraDTO,array $itens);
    public function proccessItensSale(VendasDTO $vendaDTO,array $itens);
}
