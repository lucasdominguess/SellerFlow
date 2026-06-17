<?php

namespace App\Repositories\Stock;

use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Enums\OriginType;
use App\Enums\TipoStock;
use App\Models\Stock\StockBalance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockBalanceRepository implements StockBalanceRepositoryInterface
{
    public function paginate(
        int $companyId,
        ?int $productId,
        ?string $productName,
        ?string $sku,
        int $perPage,
        int $page
    ): LengthAwarePaginator {
        return DB::table('stock_balances as b')
            ->join('products as p', 'p.id', '=', 'b.product_id')
            ->join('companies as c', 'c.id', '=', 'b.company_id')
            ->leftJoin('users as u', 'u.id', '=', 'b.last_adjustment_user_id')
            ->where('b.company_id', $companyId)
            ->when($productId, fn ($query) => $query->where('b.product_id', $productId))
            ->when($productName, fn ($query) => $query->where('p.name', 'ilike', "%{$productName}%"))
            ->when($sku, fn ($query) => $query->where('p.sku', 'ilike', "%{$sku}%"))
            ->orderBy('p.name')
            ->select([
                'b.company_id',
                'c.name as company_name',
                'b.product_id',
                'p.sku',
                'p.name as product_name',
                'u.name as last_adjustment_user',
                'b.total_entradas',
                'b.total_saidas',
                'b.total_ajustes_positivos',
                'b.total_ajustes_negativos',
                'b.saldo_atual',
            ])
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function recomputeFor(int $companyId, int $productId): void
    {
        // Mesma lógica de agregação da listagem antiga, porém escopada a um único produto.
        $totals = DB::table('stock_movements as me')
            ->leftJoin('stock_adjustments as ae', function ($join) {
                $join->on('ae.id', '=', 'me.origem_id')
                    ->where('me.origem_tipo', '=', OriginType::AJUSTE_MANUAL->value)
                    ->where('me.tipo', '=', TipoStock::AJUSTE->value);
            })
            ->where('me.company_id', $companyId)
            ->where('me.product_id', $productId)
            ->selectRaw('count(*) as movement_count')
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'entrada'), 0) as total_entradas")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'saida'), 0) as total_saidas")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade > 0), 0) as total_ajustes_positivos")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade < 0), 0) as total_ajustes_negativos")
            ->first();

        // Produto sem movimentações: remove o saldo (paridade com a listagem antiga, que só trazia produtos com movimento).
        if (! $totals || (int) $totals->movement_count === 0) {
            StockBalance::query()
                ->where('company_id', $companyId)
                ->where('product_id', $productId)
                ->delete();

            return;
        }

        // Usuário do ajuste mais recente (denormalizado para a leitura não precisar da subquery correlacionada).
        $lastAdjustmentUserId = DB::table('stock_adjustments')
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('user_id');

        $totalEntradas         = (int) $totals->total_entradas;
        $totalSaidas           = (int) $totals->total_saidas;
        $totalAjustesPositivos = (int) $totals->total_ajustes_positivos;
        $totalAjustesNegativos = (int) $totals->total_ajustes_negativos;

        StockBalance::updateOrCreate(
            ['company_id' => $companyId, 'product_id' => $productId],
            [
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'total_ajustes_positivos' => $totalAjustesPositivos,
                'total_ajustes_negativos' => $totalAjustesNegativos,
                'saldo_atual' => $totalEntradas - $totalSaidas + $totalAjustesPositivos - $totalAjustesNegativos,
                'last_adjustment_user_id' => $lastAdjustmentUserId,
            ]
        );
    }
}
