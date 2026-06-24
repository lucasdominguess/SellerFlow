<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Finance\CashFlowRepositoryInterface;
use App\DTOs\Finance\CashFlowQueryDTO;
use App\DTOs\Finance\CashFlowReportDTO;
use App\Services\Finance\CashFlowService;
use Illuminate\Support\Collection;

describe('CashFlowService', function () {

    beforeEach(function () {
        $this->repositoryMock = $this->createMock(CashFlowRepositoryInterface::class);
        $this->service        = new CashFlowService($this->repositoryMock);
        $this->dto            = new CashFlowQueryDTO(
            company_id:  1,
            start_date:  '2026-06-01',
            end_date:    '2026-07-31',
            granularity: 'month',
        );
    });

    it('acumula o saldo período a período e calcula o resumo', function () {
        $rows = new Collection([
            (object) ['period' => '2026-06-01', 'entradas' => 1000, 'saidas' => 400],
            (object) ['period' => '2026-07-01', 'entradas' => 500,  'saidas' => 700],
        ]);

        $this->repositoryMock->expects($this->once())
            ->method('realized')
            ->with($this->dto)
            ->willReturn($rows);

        $report = $this->service->realized($this->dto);
        $array  = $report->toArray();

        expect($report)->toBeInstanceOf(CashFlowReportDTO::class)
            // resumo: 1500 entradas, 1100 saídas, saldo 400
            ->and($array['summary'])->toBe(['total_entradas' => 1500.0, 'total_saidas' => 1100.0, 'saldo' => 400.0])
            // período 1: saldo 600, acumulado 600
            ->and($array['periods'][0]['saldo'])->toBe(600.0)
            ->and($array['periods'][0]['saldo_acumulado'])->toBe(600.0)
            // período 2: saldo -200, acumulado 400 (600 - 200)
            ->and($array['periods'][1]['saldo'])->toBe(-200.0)
            ->and($array['periods'][1]['saldo_acumulado'])->toBe(400.0);
    });

    it('retorna resumo zerado e sem períodos quando não há movimentações', function () {
        $this->repositoryMock->method('realized')->willReturn(new Collection());

        $report = $this->service->realized($this->dto);
        $array  = $report->toArray();

        expect($array['summary'])->toBe(['total_entradas' => 0.0, 'total_saidas' => 0.0, 'saldo' => 0.0])
            ->and($array['periods'])->toBe([]);
    });
});
