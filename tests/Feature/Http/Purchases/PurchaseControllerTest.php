<?php

namespace Tests\Feature\Http\Purchases;

use App\Models\Accout\CompanyUser;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('PurchaseController', function () {

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

        $marketplace = MarketPlace::factory()->create();
        $this->store = Store::factory()->create([
            'company_id' => $this->company->id, 'marketplace_id' => $marketplace->id,
        ]);
        DB::table('user_stores')->insert([
            'user_id' => $this->user->id, 'store_id' => $this->store->id, 'role_id' => 1, 'status_id' => 1,
        ]);

        $this->supplier = Supplier::factory()->create();
        $this->product  = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $this->supplier->id]);

        // A conta a pagar gerada pela compra usa categoria ENTRADA(1) e forma PIX(3) fixas
        // (App\Services\Finance\AccountPayableService::proccessPurchase) — em produção vêm dos seeders.
        DB::table('financial_categories')->insert([
            ['id' => 1, 'name' => 'Entrada'],
            ['id' => 2, 'name' => 'Saida'],
        ]);
        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'Débito'],
            ['id' => 2, 'name' => 'Crédito'],
            ['id' => 3, 'name' => 'PIX'],
        ]);
        $this->paymentMethodId = 3; // PIX

        $token = auth('api')->login($this->user);
        $this->withHeader('Authorization', "Bearer {$token}");

        $supplierId = $this->supplier->id;
        $productId  = $this->product->id;
        $paymentId  = $this->paymentMethodId;
        $this->payload = fn (array $override = []) => array_merge([
            'fornecedor_id'      => $supplierId,
            'forma_pagamento_id' => $paymentId,
            'numero_nota'        => 'NF-1',
            'data_compra'        => '2026-06-05',
            'numero_parcelas'    => 1,
            'itens'              => [
                ['product_id' => $productId, 'quantidade' => 5, 'valor_unitario' => 20.00],
            ],
        ], $override);
    });

    it('creates a purchase deriving valor_total, items, stock entry and a payable', function () {
        $response = $this->postJson('/api/v1/purchases', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pendente');

        // valor_total = 5 * 20
        expect((float) $response->json('data.valor_total'))->toBe(100.0);

        $purchaseId = $response->json('data.id');
        $this->assertDatabaseHas('purchases', ['id' => $purchaseId, 'company_id' => $this->company->id, 'status' => 'pendente']);
        $this->assertDatabaseHas('purchase_items', ['compra_id' => $purchaseId, 'product_id' => $this->product->id, 'quantidade' => 5]);
        $this->assertDatabaseHas('stock_movements', ['origem_tipo' => 'compra', 'origem_id' => $purchaseId, 'tipo' => 'entrada', 'quantidade' => 5]);
        $this->assertDatabaseHas('account_payables', ['origem_tipo' => 'compra', 'origem_id' => $purchaseId, 'company_id' => $this->company->id]);
        // entrada de 5 no estoque
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 5]);
    });

    it('validates that at least one item is required with 422', function () {
        $this->postJson('/api/v1/purchases', ($this->payload)(['itens' => []]))
            ->assertStatus(422);
    });

    it('reverses stock and cancels the payable when the purchase is canceled', function () {
        $purchaseId = $this->postJson('/api/v1/purchases', ($this->payload)())
            ->assertStatus(201)
            ->json('data.id');

        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 5]);

        $this->putJson("/api/v1/purchases/{$purchaseId}", ['status' => 'cancelado'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelado');

        // estorno da entrada → saldo volta a 0
        $this->assertDatabaseHas('stock_balances', ['company_id' => $this->company->id, 'product_id' => $this->product->id, 'saldo_atual' => 0]);
        $this->assertDatabaseHas('account_payables', ['origem_id' => $purchaseId, 'origem_tipo' => 'compra', 'status' => 'cancelado']);
    });

    it('shows a purchase', function () {
        $purchaseId = $this->postJson('/api/v1/purchases', ($this->payload)())->json('data.id');

        $this->getJson("/api/v1/purchases/{$purchaseId}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $purchaseId)
            ->assertJsonPath('data.fornecedor_id', $this->supplier->id);
    });

    it('lists purchases paginated', function () {
        $this->postJson('/api/v1/purchases', ($this->payload)());

        $this->getJson('/api/v1/purchases')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
