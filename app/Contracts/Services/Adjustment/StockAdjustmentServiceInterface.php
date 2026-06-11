<?php

namespace App\Contracts\Services\Adjustment;

use App\DTOs\Adjustment\StockAdjustmentDTO;
use App\DTOs\Adjustment\StockAdjustmentResponseDTO;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockAdjustmentServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(StockAdjustment $stockAdjustment): StockAdjustmentResponseDTO;

    // Um item por linha em ajustes_estoque: retorna uma ResponseDTO por item enviado
    public function store(StockAdjustmentDTO $dto): array;
}
