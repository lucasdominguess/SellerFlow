<?php

namespace App\Contracts\Services\Purchases;

use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Purchases\CompraResponseDTO;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompraServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Purchase $compra): CompraResponseDTO;

    public function store(CompraDTO $dto): CompraResponseDTO;

    public function update(Purchase $compra, CompraDTO $dto): CompraResponseDTO;

    public function delete(Purchase $compra);


}
