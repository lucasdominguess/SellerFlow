<?php

namespace App\Contracts\Repositories\Stock;

use App\Models\Purchases\Compra;
use App\Models\Sales\Venda;
use App\Models\Stock\Stock;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Stock $stock): Stock;

    public function store(array $data): Stock;

    public function update(Stock $stock, array $data): Stock;

    public function delete(Stock $stock);
}
