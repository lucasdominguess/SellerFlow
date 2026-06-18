<?php

namespace Tests\Feature\Stock;

use App\Contracts\Services\Stock\StockServiceInterface;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseItem;
use App\Models\Stock\Stock;
use App\Models\Stock\StockAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Movimentação que dispara o StockObserver (mantém o saldo materializado).
function makeStockMovement(int $companyId, int $productId, int $userId, string $tipo, int $qtd): Stock
{
    return Stock::create([
        'company_id'  => $companyId,
        'product_id'  => $productId,
        'user_id'     => $userId,
        'tipo'        => $tipo,
        'quantidade'  => $qtd,
        'origem_tipo' => $tipo === 'entrada' ? 'compra' : 'venda',
        'origem_id'   => 1,
        'observacao'  => null,
    ]);
}

describe('StockInvestment (valor investido FIFO)', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Empresa Ativa'],
            ['id' => 2, 'name' => 'Usuario Ativo'],
            ['id' => 3, 'name' => 'Produto Inativo'],
        ]);

        $this->company    = Company::factory()->create();
        $this->user       = User::factory()->create();
        $fornecedor       = Supplier::factory()->create();
        $this->product    = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $fornecedor->id]);

        MarketPlace::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id]);

        $this->paymentMethodId = DB::table('payment_methods')->insertGetId([
            'name' => 'PIX',
        ]);

        // Cria uma camada de compra (compra + item) numa data específica.
        $this->buyLayer = function (int $productId, int $qty, float $valorUnitario, string $dataCompra, string $status = 'concluido') {
            $purchase = Purchase::factory()->create([
                'company_id'         => $this->company->id,
                'store_id'           => $this->store->id,
                'fornecedor_id'      => $this->product->fornecedor_id,
                'user_id'            => $this->user->id,
                'forma_pagamento_id' => $this->paymentMethodId,
                'status'             => $status,
                'data_compra'        => $dataCompra,
            ]);

            PurchaseItem::factory()->create([
                'compra_id'      => $purchase->id,
                'product_id'     => $productId,
                'quantidade'     => $qty,
                'valor_unitario' => $valorUnitario,
                'valor_total'    => round($qty * $valorUnitario, 2),
            ]);

            return $purchase;
        };
    });

    // Cenário do usuário: 5 un @10 (mais antiga) + 5 un @12 (mais nova), vende 3.
    // FIFO: saem as 3 mais antigas (@10) -> sobram 2@10 + 5@12 = 20 + 60 = 80.
    it('valoriza o saldo pelas camadas reais de compra (FIFO)', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        ($this->buyLayer)($productId, 5, 10.00, now()->subMonths(2)->toDateString());
        ($this->buyLayer)($productId, 5, 12.00, now()->subMonth()->toDateString());

        makeStockMovement($companyId, $productId, $userId, 'entrada', 5);
        makeStockMovement($companyId, $productId, $userId, 'entrada', 5);
        makeStockMovement($companyId, $productId, $userId, 'saida', 3);

        $result = app(StockServiceInterface::class)
            ->stockInvestment($companyId, null, null, null, 15, 1);

        expect($result['total_investido'])->toBe(80.0);

        $row = $result['paginator']->items()[0];
        expect($row['saldo_atual'])->toBe(7)
            ->and($row['valor_investido'])->toBe(80.0)
            ->and($row['tem_unidade_sem_custo'])->toBeFalse()
            ->and($row['composicao'])->toBe([
                ['qty' => 5, 'preco' => 12.0],
                ['qty' => 2, 'preco' => 10.0],
            ]);
    });

    // Compra cancelada não entra no cálculo de custo.
    it('ignora compras canceladas no custo', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        ($this->buyLayer)($productId, 5, 10.00, now()->subMonths(2)->toDateString());
        ($this->buyLayer)($productId, 5, 99.00, now()->subMonth()->toDateString(), 'cancelado');

        makeStockMovement($companyId, $productId, $userId, 'entrada', 5);

        $result = app(StockServiceInterface::class)
            ->stockInvestment($companyId, null, null, null, 15, 1);

        // saldo 5, todas valorizadas a 10 (a compra de 99 foi cancelada)
        expect($result['total_investido'])->toBe(50.0);
        $row = $result['paginator']->items()[0];
        expect($row['valor_investido'])->toBe(50.0)
            ->and($row['tem_unidade_sem_custo'])->toBeFalse();
    });

    // Saldo veio de ajuste positivo sem compra correspondente -> unidades sem custo real.
    it('marca unidades sem custo quando o saldo excede o que foi comprado', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        ($this->buyLayer)($productId, 2, 10.00, now()->subMonth()->toDateString());
        makeStockMovement($companyId, $productId, $userId, 'entrada', 2);

        // Ajuste manual positivo de +3 (sem compra) -> saldo 5, mas só 2 têm custo.
        $adj = StockAdjustment::create([
            'company_id' => $companyId, 'product_id' => $productId, 'user_id' => $userId,
            'quantidade' => 3, 'motivo' => 'devolucao', 'observacao' => null,
        ]);
        Stock::create([
            'company_id' => $companyId, 'product_id' => $productId, 'user_id' => $userId,
            'tipo' => 'ajuste', 'quantidade' => 3, 'origem_tipo' => 'ajuste_manual',
            'origem_id' => $adj->id, 'observacao' => null,
        ]);

        $result = app(StockServiceInterface::class)
            ->stockInvestment($companyId, null, null, null, 15, 1);

        $row = $result['paginator']->items()[0];
        expect($row['saldo_atual'])->toBe(5)
            ->and($row['valor_investido'])->toBe(20.0) // só as 2 compradas
            ->and($row['tem_unidade_sem_custo'])->toBeTrue();
    });

    // Saldo zero/negativo não aparece na listagem.
    it('não lista produtos sem saldo positivo', function () {
        $companyId = $this->company->id;
        $productId = $this->product->id;
        $userId    = $this->user->id;

        ($this->buyLayer)($productId, 4, 10.00, now()->subMonth()->toDateString());
        makeStockMovement($companyId, $productId, $userId, 'entrada', 4);
        makeStockMovement($companyId, $productId, $userId, 'saida', 4);

        $result = app(StockServiceInterface::class)
            ->stockInvestment($companyId, null, null, null, 15, 1);

        expect($result['paginator']->total())->toBe(0)
            ->and($result['total_investido'])->toBe(0.0);
    });
});
