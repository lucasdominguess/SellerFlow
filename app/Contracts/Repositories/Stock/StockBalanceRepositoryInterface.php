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

    // Listagem paginada do valor investido (FIFO) por produto com saldo positivo.
    public function paginateInvestment(
        int $companyId,
        ?int $productId,
        ?string $productName,
        ?string $sku,
        int $perPage,
        int $page
    ): LengthAwarePaginator;

    // Soma total do valor investido no estoque, respeitando os mesmos filtros da listagem.
    public function totalInvested(
        int $companyId,
        ?int $productId,
        ?string $productName,
        ?string $sku
    ): float;

    // Recalcula (upsert) o saldo de um produto a partir de stock_movements.
    // Se o produto não tiver mais movimentações, remove a linha de saldo.
    public function recomputeFor(int $companyId, int $productId): void;

    // Lê saldos travando as linhas (FOR UPDATE) até o commit, serializando vendas
    // concorrentes. Deve rodar dentro de uma transação. Retorna [product_id => saldo].
    public function lockAvailableQuantities(int $companyId, array $productIds): array;
}
