<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildStockBalances extends Command
{
    protected $signature = 'stock:rebuild-balances';

    protected $description = 'Recalcula stock_balances a partir de movimentacoes_estoque (backfill / reparo)';

    public function handle(StockBalanceRepositoryInterface $balanceRepository): int
    {
        $pairs = DB::table('movimentacoes_estoque')
            ->select('company_id', 'product_id')
            ->distinct()
            ->get();

        $this->info("Recalculando saldo de {$pairs->count()} produto(s)...");

        foreach ($pairs as $pair) {
            $balanceRepository->recomputeFor((int) $pair->company_id, (int) $pair->product_id);
        }

        $this->info('Saldos reconstruidos com sucesso.');

        return self::SUCCESS;
    }
}
