<?php

namespace App\Repositories\Finance;

use App\Contracts\Repositories\Finance\CashFlowRepositoryInterface;
use App\DTOs\Finance\CashFlowQueryDTO;
use App\Enums\TransactionStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CashFlowRepository implements CashFlowRepositoryInterface
{
    // Fluxo de caixa realizado: une as contas a receber recebidas (entradas) e as contas
    // a pagar pagas (saídas), agregando por período. Só conta o que foi efetivamente
    // liquidado (status = concluido), pela data real (recebido_em / pago_em).
    public function realized(CashFlowQueryDTO $dto): Collection
    {
        // granularidade vem de um allowlist fixo (validado no FormRequest); mapear aqui
        // garante que apenas literais conhecidos cheguem ao date_trunc — sem injeção.
        $unit = match ($dto->granularity) {
            'day'   => 'day',
            'week'  => 'week',
            default => 'month',
        };

        $entradas = DB::table('account_receivables')
            ->selectRaw("date_trunc('{$unit}', recebido_em) as period, valor as entrada, 0 as saida")
            ->where('company_id', $dto->company_id)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->whereBetween('recebido_em', [$dto->start_date, $dto->end_date]);

        $saidas = DB::table('account_payables')
            ->selectRaw("date_trunc('{$unit}', pago_em) as period, 0 as entrada, valor as saida")
            ->where('company_id', $dto->company_id)
            ->where('status', TransactionStatus::COMPLETED->value)
            ->whereBetween('pago_em', [$dto->start_date, $dto->end_date]);

        return DB::query()
            ->fromSub($entradas->unionAll($saidas), 't')
            ->selectRaw('period, sum(entrada) as entradas, sum(saida) as saidas')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }
}
