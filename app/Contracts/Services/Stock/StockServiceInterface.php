<?php

namespace App\Contracts\Services\Stock;

use App\DTOs\Adjustment\StockAdjustmentDTO;
use App\DTOs\Stock\StockDTO;
use App\DTOs\Stock\StockResponseDTO;
use App\Models\Purchases\Compra;
use App\Models\Sales\Venda;
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

    public function proccessItensPurchase(Compra $compra,array $itens);
    public function proccessItensSale(Venda $venda,array $itens);

    public function proccessItensAdjustment(StockAdjustment $stockAdjustment);

    public function checkQuantityProductsInStock(int $companyId, ?int $productId = null, ?string $productName = null, ?string $sku = null): array;
}
