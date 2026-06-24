<?php

namespace Tests\Feature\Http\Stock;

use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\Stock\StockAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('StockAdjustmentController', function () {

    beforeEach(function () {
        // Supplier/Product factories sorteiam status_id entre 1 e 3
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);

        // ajuste tem company_id (tenant scope): autentica usuário vinculado a uma empresa
        $ctx = actingAsCompanyJwt();
        $this->company = $ctx['company'];
        $this->user    = $ctx['user'];

        $supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);
    });

    it('cria ajustes, movimentos de estoque e recalcula o saldo', function () {
        $response = $this->postJson('/api/v1/stock-adjustment', [
            'itens' => [
                ['product_id' => $this->product->id, 'quantidade' => 5, 'motivo' => 'devolucao'],
                ['product_id' => $this->product->id, 'quantidade' => -2, 'motivo' => 'perda'],
            ],
        ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        expect($response->json('data'))->toHaveCount(2);

        $this->assertDatabaseCount('stock_adjustments', 2);
        $this->assertDatabaseHas('stock_adjustments', [
            'company_id' => $this->company->id, 'product_id' => $this->product->id, 'quantidade' => 5, 'motivo' => 'devolucao',
        ]);
        // cada ajuste gera um movimento tipo 'ajuste' com a quantidade absoluta
        $this->assertDatabaseHas('stock_movements', [
            'company_id' => $this->company->id, 'product_id' => $this->product->id,
            'tipo' => 'ajuste', 'origem_tipo' => 'ajuste_manual', 'quantidade' => 2,
        ]);
        // saldo materializado: +5 -2 = 3
        $this->assertDatabaseHas('stock_balances', [
            'company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 3,
        ]);
    });

    it('rejeita um item com quantidade zero com 422', function () {
        $this->postJson('/api/v1/stock-adjustment', [
            'itens' => [['product_id' => $this->product->id, 'quantidade' => 0, 'motivo' => 'perda']],
        ])->assertStatus(422);
    });

    it('rejeita um motivo inválido com 422', function () {
        $this->postJson('/api/v1/stock-adjustment', [
            'itens' => [['product_id' => $this->product->id, 'quantidade' => 3, 'motivo' => 'invalido']],
        ])->assertStatus(422);
    });

    it('exige ao menos um item com 422', function () {
        $this->postJson('/api/v1/stock-adjustment', ['itens' => []])->assertStatus(422);
    });

    it('exibe um ajuste', function () {
        $adj = StockAdjustment::create([
            'company_id' => $this->company->id, 'product_id' => $this->product->id,
            'user_id' => $this->user->id, 'quantidade' => 7, 'motivo' => 'devolucao', 'observacao' => null,
        ]);

        $this->getJson("/api/v1/stock-adjustment/{$adj->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $adj->id)
            ->assertJsonPath('data.quantidade', 7);
    });

    it('lista ajustes paginados', function () {
        StockAdjustment::create([
            'company_id' => $this->company->id, 'product_id' => $this->product->id,
            'user_id' => $this->user->id, 'quantidade' => 7, 'motivo' => 'devolucao', 'observacao' => null,
        ]);

        $this->getJson('/api/v1/stock-adjustment')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
