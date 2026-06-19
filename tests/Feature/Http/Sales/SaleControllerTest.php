<?php

namespace Tests\Feature\Http\Sales;

use App\Models\Accout\CompanyUser;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Stock\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Semeia estoque (ENTRADA) para um produto, disparando o StockObserver que materializa o saldo.
function seedStockForSale(int $companyId, int $productId, int $userId, int $qty): void
{
    Stock::create([
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

describe('SaleController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);
        DB::table('roles')->insert([['id' => 1, 'name' => 'admin'], ['id' => 2, 'name' => 'user']]);

        $this->company = Company::factory()->create();
        $this->user    = User::factory()->create(['status_id' => 1]);
        CompanyUser::factory()->create([
            'company_id' => $this->company->id, 'user_id' => $this->user->id, 'role_id' => 1, 'status_id' => 1,
        ]);

        $this->marketplace = MarketPlace::factory()->create();
        $this->store = Store::factory()->create([
            'company_id' => $this->company->id, 'marketplace_id' => $this->marketplace->id,
        ]);
        // vínculo user↔loja: o store_id da venda vem de storeIds() no JWT
        DB::table('user_stores')->insert([
            'user_id' => $this->user->id, 'store_id' => $this->store->id, 'role_id' => 1, 'status_id' => 1,
        ]);

        $supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);

        // autentica DEPOIS dos vínculos (claims company/stores do token)
        $token = auth('api')->login($this->user);
        $this->withHeader('Authorization', "Bearer {$token}");

        $marketplaceId = $this->marketplace->id;
        $productId     = $this->product->id;
        $this->payload = fn (array $override = []) => array_merge([
            'market_place_id'  => $marketplaceId,
            'numero_pedido'    => 'PED-1001',
            'data_venda'       => '2026-06-10',
            'valor_bruto'      => 200.00,
            'taxa_marketplace' => 30.00,
            'valor_frete'      => 10.00,
            'venda_itens'      => [
                ['product_id' => $productId, 'quantidade' => 2, 'valor_unitario' => 100.00],
            ],
        ], $override);
    });

    it('creates a sale deriving valor_liquido, items, stock exit and a receivable', function () {
        seedStockForSale($this->company->id, $this->product->id, $this->user->id, 10);

        $response = $this->postJson('/api/v1/sales', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pendente');

        // valor_liquido = 200 - 30 - 10
        expect((float) $response->json('data.valor_liquido'))->toBe(160.0);

        $saleId = $response->json('data.id');
        $this->assertDatabaseHas('sales', ['id' => $saleId, 'company_id' => $this->company->id, 'status' => 'pendente']);
        $this->assertDatabaseHas('sale_items', ['venda_id' => $saleId, 'product_id' => $this->product->id, 'quantidade' => 2]);
        $this->assertDatabaseHas('stock_movements', ['origem_tipo' => 'venda', 'origem_id' => $saleId, 'tipo' => 'saida', 'quantidade' => 2]);
        $this->assertDatabaseHas('account_receivables', ['origem_tipo' => 'venda', 'origem_id' => $saleId, 'company_id' => $this->company->id]);
        // saldo: 10 - 2 = 8
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 8]);
    });

    it('blocks overselling with 422 and persists nothing', function () {
        seedStockForSale($this->company->id, $this->product->id, $this->user->id, 1); // só 1 em estoque

        $this->postJson('/api/v1/sales', ($this->payload)([
            'venda_itens' => [['product_id' => $this->product->id, 'quantidade' => 5, 'valor_unitario' => 100.00]],
        ]))->assertStatus(422);

        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('account_receivables', 0);
    });

    it('validates that at least one item is required with 422', function () {
        $this->postJson('/api/v1/sales', ($this->payload)(['venda_itens' => []]))
            ->assertStatus(422);
    });

    it('reverses stock and cancels the receivable when the sale is canceled', function () {
        seedStockForSale($this->company->id, $this->product->id, $this->user->id, 10);

        $saleId = $this->postJson('/api/v1/sales', ($this->payload)())
            ->assertStatus(201)
            ->json('data.id');

        // saldo após vender 2 = 8
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 8]);

        $this->putJson("/api/v1/sales/{$saleId}", ['status' => 'cancelado'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelado');

        // estorno devolve 2 ao estoque → saldo volta a 10
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 10]);
        $this->assertDatabaseHas('account_receivables', ['origem_id' => $saleId, 'origem_tipo' => 'venda', 'status' => 'cancelado']);
    });

    it('shows a sale', function () {
        seedStockForSale($this->company->id, $this->product->id, $this->user->id, 10);
        $saleId = $this->postJson('/api/v1/sales', ($this->payload)())->json('data.id');

        $this->getJson("/api/v1/sales/{$saleId}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $saleId)
            ->assertJsonPath('data.numero_pedido', 'PED-1001');
    });

    it('lists sales paginated', function () {
        seedStockForSale($this->company->id, $this->product->id, $this->user->id, 10);
        $this->postJson('/api/v1/sales', ($this->payload)());

        $this->getJson('/api/v1/sales')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
