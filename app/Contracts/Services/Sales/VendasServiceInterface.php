<?php

namespace App\Contracts\Services\Sales;

use App\DTOs\Sales\VendasDTO;
use App\DTOs\Sales\VendasResponseDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendasServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Sale $venda): VendasResponseDTO;

    public function store(VendasDTO $dto): VendasResponseDTO;

    public function update(Sale $venda, VendasDTO $dto): VendasResponseDTO;

    public function delete(Sale $venda);
}
