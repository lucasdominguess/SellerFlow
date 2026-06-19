<?php

namespace App\Services\Finance;

use App\Contracts\Repositories\Finance\DashboardRepositoryInterface;
use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Contracts\Services\Finance\DashboardServiceInterface;
use App\DTOs\Finance\DashboardQueryDTO;
use App\DTOs\Finance\DashboardResponseDTO;

class DashboardService implements DashboardServiceInterface
{
    public function __construct(
        private DashboardRepositoryInterface $repository,
        // total_investido reaproveita o cálculo FIFO já existente do estoque
        private StockBalanceRepositoryInterface $balanceRepository,
    ) {}

    public function build(DashboardQueryDTO $dto): DashboardResponseDTO
    {
        $estoque = $this->repository->stockCounts($dto);
        $estoque['total_investido'] = round(
            $this->balanceRepository->totalInvested($dto->company_id, null, null, null),
            2
        );

        return new DashboardResponseDTO(
            start_date:   $dto->start_date,
            end_date:     $dto->end_date,
            vendas:       $this->repository->salesSummary($dto),
            compras:      $this->repository->purchasesSummary($dto),
            a_receber:    $this->repository->receivablesSummary($dto),
            a_pagar:      $this->repository->payablesSummary($dto),
            estoque:      $estoque,
            top_produtos: $this->repository->topProducts($dto)
                ->map(fn ($row) => [
                    'product_id' => (int) $row->product_id,
                    'sku'        => $row->sku,
                    'name'       => $row->name,
                    'quantidade' => (int) $row->quantidade,
                ])
                ->all(),
        );
    }
}
