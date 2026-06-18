<?php

namespace App\Contracts\Services\Stock;

use App\DTOs\Adjustment\StockAdjustmentDTO;
use App\DTOs\Stock\CheckStockQuantityDTO;
use App\DTOs\Stock\StockDTO;
use App\DTOs\Stock\StockInvestmentQueryDTO;
use App\DTOs\Stock\StockResponseDTO;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use App\Models\Stock\Stock;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Stock $stock): StockResponseDTO;

    public function store(StockDTO $dto): StockResponseDTO;

    public function update(Stock $stock, StockDTO $dto): StockResponseDTO;

    public function delete(Stock $stock);

    public function proccessItensPurchase(Purchase $compra,array $itens);
    public function proccessItensSale(Sale $venda,array $itens);

    public function reverseItensPurchase(Purchase $compra);
    public function reverseItensSale(Sale $venda);

    public function proccessItensAdjustment(StockAdjustment $stockAdjustment);

    public function checkQuantityProductsInStock(CheckStockQuantityDTO $dto): LengthAwarePaginator;

    // Retorna ['total_investido' => float, 'paginator' => LengthAwarePaginator].
    public function stockInvestment(StockInvestmentQueryDTO $dto): array;
}
