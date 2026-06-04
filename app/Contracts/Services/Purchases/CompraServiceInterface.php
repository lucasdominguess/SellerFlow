<?php

namespace App\Contracts\Services\Purchases;

use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Purchases\CompraResponseDTO;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompraServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Compra $compra): CompraResponseDTO;

    public function store(CompraDTO $dto): CompraResponseDTO;

    public function update(Compra $compra, CompraDTO $dto): CompraResponseDTO;

    public function delete(Compra $compra);


}
