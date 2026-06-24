<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use App\Models\Stock\StockAdjustment;
use App\Services\Stock\StockService;
use Illuminate\Support\Collection;

describe('StockService (estorno e processamento de movimentos)', function () {

    beforeEach(function () {
        $this->repositoryMock = $this->createMock(StockRepositoryInterface::class);
        $this->balanceMock    = $this->createMock(StockBalanceRepositoryInterface::class);
        $this->service        = new StockService($this->repositoryMock, $this->balanceMock);
    });

    it('estorna a compra gerando uma SAIDA para cada item', function () {
        $purchase = new Purchase(['company_id' => 1, 'user_id' => 2]);
        $purchase->id = 10;
        $purchase->setRelation('itens', new Collection([
            (object) ['product_id' => 5, 'quantidade' => 3],
        ]));

        $this->repositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(fn (array $d) =>
                $d['tipo'] === 'saida'
                && $d['quantidade'] === 3
                && $d['product_id'] === 5
                && $d['origem_tipo'] === 'compra'
                && $d['origem_id'] === 10
                && $d['company_id'] === 1
            ));

        $this->service->reverseItensPurchase($purchase);
    });

    it('estorna a venda gerando uma ENTRADA para cada item', function () {
        $sale = new Sale(['company_id' => 1, 'user_id' => 2]);
        $sale->id = 20;
        $sale->setRelation('itens', new Collection([
            (object) ['product_id' => 7, 'quantidade' => 4],
        ]));

        $this->repositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(fn (array $d) =>
                $d['tipo'] === 'entrada'
                && $d['quantidade'] === 4
                && $d['product_id'] === 7
                && $d['origem_tipo'] === 'venda'
                && $d['origem_id'] === 20
            ));

        $this->service->reverseItensSale($sale);
    });

    it('processa um ajuste usando a quantidade absoluta no movimento', function () {
        // ajuste negativo (-2): o movimento de estoque é sempre positivo
        $adjustment = new StockAdjustment([
            'company_id' => 1, 'product_id' => 5, 'user_id' => 2, 'quantidade' => -2, 'observacao' => null,
        ]);
        $adjustment->id = 7;

        $this->repositoryMock->expects($this->once())
            ->method('store')
            ->with($this->callback(fn (array $d) =>
                $d['tipo'] === 'ajuste'
                && $d['quantidade'] === 2 // abs(-2)
                && $d['origem_tipo'] === 'ajuste_manual'
                && $d['origem_id'] === 7
            ));

        $this->service->proccessItensAdjustment($adjustment);
    });
});
