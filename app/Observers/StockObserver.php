<?php

namespace App\Observers;

use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Models\Stock\Stock;

class StockObserver
{
    public function __construct(
        private StockBalanceRepositoryInterface $balanceRepository,
    ) {}

    public function created(Stock $stock): void
    {
        $this->balanceRepository->recomputeFor((int) $stock->company_id, (int) $stock->product_id);
    }

    public function updated(Stock $stock): void
    {
        // Se a movimentação mudou de empresa/produto, recalcula também o saldo de origem.
        if ($stock->wasChanged('company_id') || $stock->wasChanged('product_id')) {
            $this->balanceRepository->recomputeFor(
                (int) $stock->getOriginal('company_id'),
                (int) $stock->getOriginal('product_id'),
            );
        }

        $this->balanceRepository->recomputeFor((int) $stock->company_id, (int) $stock->product_id);
    }

    public function deleted(Stock $stock): void
    {
        $this->balanceRepository->recomputeFor((int) $stock->company_id, (int) $stock->product_id);
    }
}
