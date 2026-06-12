<?php

namespace App\Services\Finance;

use App\Contracts\Repositories\Finance\CashFlowRepositoryInterface;
use App\Contracts\Services\Finance\CashFlowServiceInterface;
use App\DTOs\Finance\CashFlowEntryDTO;
use App\DTOs\Finance\CashFlowQueryDTO;
use App\DTOs\Finance\CashFlowReportDTO;

class CashFlowService implements CashFlowServiceInterface
{
    public function __construct(
        private CashFlowRepositoryInterface $repository,
    ) {}

    public function realized(CashFlowQueryDTO $dto): CashFlowReportDTO
    {
        $rows = $this->repository->realized($dto);

        $totalEntradas = 0.0;
        $totalSaidas   = 0.0;
        $acumulado     = 0.0;

        // percorre os períodos em ordem mantendo o saldo acumulado (regra de negócio)
        $periods = $rows->map(function (object $row) use (&$totalEntradas, &$totalSaidas, &$acumulado) {
            $totalEntradas += (float) $row->entradas;
            $totalSaidas   += (float) $row->saidas;
            $acumulado     += (float) $row->entradas - (float) $row->saidas;

            return CashFlowEntryDTO::fromQueryResult($row, $acumulado);
        })->all();

        $summary = [
            'total_entradas' => round($totalEntradas, 2),
            'total_saidas'   => round($totalSaidas, 2),
            'saldo'          => round($totalEntradas - $totalSaidas, 2),
        ];

        return new CashFlowReportDTO(
            granularity: $dto->granularity,
            start_date:  $dto->start_date,
            end_date:    $dto->end_date,
            summary:     $summary,
            periods:     $periods,
        );
    }
}
