<?php

namespace App\Contracts\Repositories\Purchases;

use App\DTOs\Purchases\PurchaseItemDTO;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Purchase $purchase): Purchase;

    public function store(array $data): Purchase;

    /** @param PurchaseItemDTO[] $itens */
    public function storeItens(Purchase $purchase, array $itens): Purchase;

    public function update(Purchase $purchase, array $data): Purchase;

    public function delete(Purchase $purchase);
}
