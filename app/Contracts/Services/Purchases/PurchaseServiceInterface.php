<?php

namespace App\Contracts\Services\Purchases;

use App\DTOs\Purchases\PurchaseDTO;
use App\DTOs\Purchases\PurchaseResponseDTO;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Purchase $purchase): PurchaseResponseDTO;

    public function store(PurchaseDTO $dto): PurchaseResponseDTO;

    public function update(Purchase $purchase, PurchaseDTO $dto): PurchaseResponseDTO;

    public function delete(Purchase $purchase);


}
