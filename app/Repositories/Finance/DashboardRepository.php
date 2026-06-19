<?php

namespace App\Repositories\Finance;

use App\Contracts\Repositories\Finance\DashboardRepositoryInterface;
use App\DTOs\Finance\DashboardQueryDTO;
use App\Enums\TransactionStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function salesSummary(DashboardQueryDTO $dto): array
    {
        $base = DB::table('sales')
            ->where('company_id', $dto->company_id)
            ->whereBetween('data_venda', [$dto->start_date, $dto->end_date])
            ->where('status', '<>', TransactionStatus::CANCELED->value);

        return [
            'pedidos'       => (clone $base)->count(),
            'total_bruto'   => round((float) (clone $base)->sum('valor_bruto'), 2),
            'total_liquido' => round((float) (clone $base)->sum('valor_liquido'), 2),
        ];
    }

    public function purchasesSummary(DashboardQueryDTO $dto): array
    {
        $base = DB::table('purchases')
            ->where('company_id', $dto->company_id)
            ->whereBetween('data_compra', [$dto->start_date, $dto->end_date])
            ->where('status', '<>', TransactionStatus::CANCELED->value);

        return [
            'compras' => (clone $base)->count(),
            'total'   => round((float) (clone $base)->sum('valor_total'), 2),
        ];
    }

    public function receivablesSummary(DashboardQueryDTO $dto): array
    {
        return $this->financeSummary(
            table: 'account_receivables',
            companyId: $dto->company_id,
            dueColumn: 'previsao_recebimento',
            settledColumn: 'recebido_em',
            settledKey: 'recebido_periodo',
            start: $dto->start_date,
            end: $dto->end_date,
        );
    }

    public function payablesSummary(DashboardQueryDTO $dto): array
    {
        return $this->financeSummary(
            table: 'account_payables',
            companyId: $dto->company_id,
            dueColumn: 'vencimento',
            settledColumn: 'pago_em',
            settledKey: 'pago_periodo',
            start: $dto->start_date,
            end: $dto->end_date,
        );
    }

    public function stockCounts(DashboardQueryDTO $dto): array
    {
        $base = DB::table('stock_balances')->where('company_id', $dto->company_id);

        return [
            'skus_com_saldo' => (clone $base)->where('saldo_atual', '>', 0)->count(),
            'skus_zerados'   => (clone $base)->where('saldo_atual', '<=', 0)->count(),
        ];
    }

    public function topProducts(DashboardQueryDTO $dto): Collection
    {
        return DB::table('sale_items as si')
            ->join('sales as s', 's.id', '=', 'si.venda_id')
            ->join('products as p', 'p.id', '=', 'si.product_id')
            ->where('s.company_id', $dto->company_id)
            ->whereBetween('s.data_venda', [$dto->start_date, $dto->end_date])
            ->where('s.status', '<>', TransactionStatus::CANCELED->value)
            ->groupBy('si.product_id', 'p.sku', 'p.name')
            ->orderByDesc('quantidade')
            ->limit(5)
            ->get([
                'si.product_id',
                'p.sku',
                'p.name',
                DB::raw('sum(si.quantidade) as quantidade'),
            ]);
    }

    // Esqueleto comum a contas a pagar/receber. "atrasado" é calculado dinamicamente
    // pela data de vencimento (não depende de um job que vire o status), e "a vencer"
    // é cumulativo (próximos 30 dias inclui os próximos 7).
    private function financeSummary(
        string $table,
        int $companyId,
        string $dueColumn,
        string $settledColumn,
        string $settledKey,
        string $start,
        string $end,
    ): array {
        $base = fn () => DB::table($table)->where('company_id', $companyId);

        $today  = Carbon::now()->toDateString();
        $in7d   = Carbon::now()->addDays(7)->toDateString();
        $in30d  = Carbon::now()->addDays(30)->toDateString();

        $pending = TransactionStatus::PENDING->value;
        $done    = TransactionStatus::COMPLETED->value;

        return [
            'pendente'    => round((float) $base()->where('status', $pending)->sum('valor'), 2),
            $settledKey   => round((float) $base()->where('status', $done)
                                ->whereBetween($settledColumn, [$start, $end])->sum('valor'), 2),
            'atrasado'    => round((float) $base()->where('status', $pending)
                                ->whereDate($dueColumn, '<', $today)->sum('valor'), 2),
            'a_vencer_7d' => round((float) $base()->where('status', $pending)
                                ->whereBetween($dueColumn, [$today, $in7d])->sum('valor'), 2),
            'a_vencer_30d' => round((float) $base()->where('status', $pending)
                                ->whereBetween($dueColumn, [$today, $in30d])->sum('valor'), 2),
        ];
    }
}
