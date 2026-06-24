<?php

namespace Tests\Feature\Http\Stock;

use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\Stock\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Movimento de ENTRADA que dispara o StockObserver (materializa o saldo).
function makeEntrada(int $companyId, int $productId, int $userId, int $qty): Stock
{
    return Stock::create([
        'company_id'  => $companyId,
        'product_id'  => $productId,
        'user_id'     => $userId,
        'tipo'        => 'entrada',
        'quantidade'  => $qty,
        'origem_tipo' => 'compra',
        'origem_id'   => 1,
        'observacao'  => null,
    ]);
}

describe('StockController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);

        $ctx = actingAsCompanyJwt();
        $this->company = $ctx['company'];
        $this->user    = $ctx['user'];

        $supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);
    });

    // --- GET /api/v1/stock-check-quantity (saldo materializado) ---

    it('retorna o saldo materializado da empresa do usuário', function () {
        makeEntrada($this->company->id, $this->product->id, $this->user->id, 10);

        $response = $this->getJson('/api/v1/stock-check-quantity')->assertStatus(200);

        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.product_id'))->toBe($this->product->id)
            ->and($response->json('data.0.saldo_atual'))->toBe(10);
    });

    it('filtra o saldo por sku', function () {
        // o filtro usa ilike (operador exclusivo do Postgres)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('ilike só existe no Postgres.');
        }

        makeEntrada($this->company->id, $this->product->id, $this->user->id, 10);

        $other = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $this->product->fornecedor_id]);
        makeEntrada($this->company->id, $other->id, $this->user->id, 5);

        $response = $this->getJson('/api/v1/stock-check-quantity?sku=' . $this->product->sku)
            ->assertStatus(200);

        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.product_id'))->toBe($this->product->id);
    });

    it('não retorna o saldo de outra empresa', function () {
        makeEntrada($this->company->id, $this->product->id, $this->user->id, 10);

        // saldo de outra empresa não deve aparecer (check-quantity filtra pelo company_id do JWT)
        $otherCompanyId = DB::table('companies')->insertGetId(['name' => 'Outra', 'status_id' => 1]);
        makeEntrada($otherCompanyId, $this->product->id, $this->user->id, 99);

        $response = $this->getJson('/api/v1/stock-check-quantity')->assertStatus(200);

        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.company_id'))->toBe($this->company->id);
    });

    // --- GET /api/v1/stock-investment (view FIFO — só Postgres) ---

    it('retorna o relatório de investimento em estoque', function () {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('stock_investment_view só existe no Postgres.');
        }

        $this->getJson('/api/v1/stock-investment')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data', 'meta', 'total_investido']);
    });

    // --- apiResource stock (livro-razão bruto) ---

    it('lista os movimentos brutos de estoque paginados', function () {
        makeEntrada($this->company->id, $this->product->id, $this->user->id, 10);

        $this->getJson('/api/v1/stock')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });

    it('exclui um movimento de estoque e recalcula o saldo', function () {
        $movement = makeEntrada($this->company->id, $this->product->id, $this->user->id, 10);
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 10]);

        $this->deleteJson("/api/v1/stock/{$movement->id}")->assertStatus(200);

        $this->assertDatabaseMissing('stock_movements', ['id' => $movement->id]);
        // sem movimentos restantes, a linha de saldo é removida pelo observer
        $this->assertDatabaseMissing('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id]);
    });
});
