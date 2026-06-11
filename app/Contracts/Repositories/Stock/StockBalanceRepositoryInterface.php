<?php

namespace App\Contracts\Repositories\Stock;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockBalanceRepositoryInterface
{
    public function paginate(
        int $companyId,
        ?int $productId,
        ?string $productName,
        ?string $sku,
        int $perPage,
        int $page
    ): LengthAwarePaginator;

    // Recalcula (upsert) o saldo de um produto a partir de movimentacoes_estoque.
    // Se o produto não tiver mais movimentações, remove a linha de saldo.
    public function recomputeFor(int $companyId, int $productId): void;
}
