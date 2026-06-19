<?php

namespace App\Contracts\Repositories\Finance;

use App\DTOs\Finance\DashboardQueryDTO;
use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    // ['pedidos' => int, 'total_bruto' => float, 'total_liquido' => float]
    public function salesSummary(DashboardQueryDTO $dto): array;

    // ['compras' => int, 'total' => float]
    public function purchasesSummary(DashboardQueryDTO $dto): array;

    // ['pendente','recebido_periodo','atrasado','a_vencer_7d','a_vencer_30d'] (floats)
    public function receivablesSummary(DashboardQueryDTO $dto): array;

    // ['pendente','pago_periodo','atrasado','a_vencer_7d','a_vencer_30d'] (floats)
    public function payablesSummary(DashboardQueryDTO $dto): array;

    // ['skus_com_saldo' => int, 'skus_zerados' => int]
    public function stockCounts(DashboardQueryDTO $dto): array;

    // Top 5 produtos do período por quantidade vendida: { product_id, sku, name, quantidade }
    public function topProducts(DashboardQueryDTO $dto): Collection;
}
