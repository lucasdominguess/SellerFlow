<?php

namespace App\Contracts\Services\Sales;

use App\DTOs\Sales\SaleDTO;
use App\DTOs\Sales\SaleResponseDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Sale $sale): SaleResponseDTO;

    public function store(SaleDTO $dto): SaleResponseDTO;

    public function update(Sale $sale, SaleDTO $dto): SaleResponseDTO;

    public function delete(Sale $sale);
}
