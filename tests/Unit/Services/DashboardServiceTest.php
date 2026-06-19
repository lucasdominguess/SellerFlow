<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Finance\DashboardRepositoryInterface;
use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\DTOs\Finance\DashboardQueryDTO;
use App\DTOs\Finance\DashboardResponseDTO;
use App\Services\Finance\DashboardService;
use Illuminate\Support\Collection;

describe('DashboardService', function () {

    beforeEach(function () {
        $this->repositoryMock = $this->createMock(DashboardRepositoryInterface::class);
        $this->balanceMock    = $this->createMock(StockBalanceRepositoryInterface::class);
        $this->service        = new DashboardService($this->repositoryMock, $this->balanceMock);

        $this->dto = new DashboardQueryDTO(
            company_id: 1,
            start_date: '2026-06-01',
            end_date:   '2026-06-30',
        );
    });

    // monta o relatório agregando os blocos do repository + o total investido do estoque
    it('builds the dashboard report from the metric blocks', function () {
        $this->repositoryMock->method('salesSummary')
            ->willReturn(['pedidos' => 3, 'total_bruto' => 500.00, 'total_liquido' => 420.00]);
        $this->repositoryMock->method('purchasesSummary')
            ->willReturn(['compras' => 2, 'total' => 300.00]);
        $this->repositoryMock->method('receivablesSummary')
            ->willReturn(['pendente' => 80.0, 'recebido_periodo' => 420.0, 'atrasado' => 10.0, 'a_vencer_7d' => 30.0, 'a_vencer_30d' => 70.0]);
        $this->repositoryMock->method('payablesSummary')
            ->willReturn(['pendente' => 50.0, 'pago_periodo' => 250.0, 'atrasado' => 0.0, 'a_vencer_7d' => 20.0, 'a_vencer_30d' => 50.0]);
        $this->repositoryMock->method('stockCounts')
            ->willReturn(['skus_com_saldo' => 5, 'skus_zerados' => 1]);
        $this->repositoryMock->method('topProducts')
            ->willReturn(new Collection([
                (object) ['product_id' => 10, 'sku' => 'ABC', 'name' => 'Produto A', 'quantidade' => 9],
            ]));

        $this->balanceMock->expects($this->once())
            ->method('totalInvested')
            ->with(1, null, null, null)
            ->willReturn(1234.56);

        $result = $this->service->build($this->dto);
        $array  = $result->toArray();

        expect($result)->toBeInstanceOf(DashboardResponseDTO::class)
            ->and($array['periodo'])->toBe(['inicio' => '2026-06-01', 'fim' => '2026-06-30'])
            ->and($array['vendas']['pedidos'])->toBe(3)
            ->and($array['vendas']['total_liquido'])->toBe(420.00)
            ->and($array['compras']['compras'])->toBe(2)
            ->and($array['financeiro']['a_receber']['atrasado'])->toBe(10.0)
            ->and($array['financeiro']['a_pagar']['pago_periodo'])->toBe(250.0)
            ->and($array['estoque']['skus_com_saldo'])->toBe(5)
            ->and($array['estoque']['total_investido'])->toBe(1234.56)
            ->and($array['top_produtos'])->toBe([
                ['product_id' => 10, 'sku' => 'ABC', 'name' => 'Produto A', 'quantidade' => 9],
            ]);
    });

    // o total investido reaproveita o cálculo FIFO do StockBalanceRepository (sem filtros)
    it('reuses StockBalanceRepository::totalInvested for the invested amount', function () {
        $this->repositoryMock->method('salesSummary')->willReturn(['pedidos' => 0, 'total_bruto' => 0.0, 'total_liquido' => 0.0]);
        $this->repositoryMock->method('purchasesSummary')->willReturn(['compras' => 0, 'total' => 0.0]);
        $this->repositoryMock->method('receivablesSummary')->willReturn(['pendente' => 0.0, 'recebido_periodo' => 0.0, 'atrasado' => 0.0, 'a_vencer_7d' => 0.0, 'a_vencer_30d' => 0.0]);
        $this->repositoryMock->method('payablesSummary')->willReturn(['pendente' => 0.0, 'pago_periodo' => 0.0, 'atrasado' => 0.0, 'a_vencer_7d' => 0.0, 'a_vencer_30d' => 0.0]);
        $this->repositoryMock->method('stockCounts')->willReturn(['skus_com_saldo' => 0, 'skus_zerados' => 0]);
        $this->repositoryMock->method('topProducts')->willReturn(new Collection());

        $this->balanceMock->method('totalInvested')->willReturn(0.0);

        $result = $this->service->build($this->dto);

        expect($result->estoque['total_investido'])->toBe(0.0)
            ->and($result->top_produtos)->toBe([]);
    });
});
