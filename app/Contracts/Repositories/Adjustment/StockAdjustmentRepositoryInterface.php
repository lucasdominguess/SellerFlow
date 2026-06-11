<?php

namespace App\Contracts\Repositories\Adjustment;


use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockAdjustmentRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(StockAdjustment $stockAdjustment): StockAdjustment;

    public function store(array $data): StockAdjustment;
}
