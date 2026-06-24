<?php

namespace Tests\Feature\Http\Business;

use App\Models\Accout\User;
use App\Models\Business\ValidateProduct;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('ValidateProductController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);
        DB::table('roles')->insert([['id' => 1, 'name' => 'admin']]);

        // company_id é exigido (tenant scope) e vem do JWT
        $ctx = actingAsCompanyJwt();
        $this->company = $ctx['company'];

        $this->marketplace = MarketPlace::factory()->create([
            'taxa_percentual' => 20.00,
            'taxa_fixa'       => 4.00,
        ]);

        $marketplaceId = $this->marketplace->id;
        $this->payload = fn (array $override = []) => array_merge([
            'name'            => 'Capa de Celular',
            'price_sale'      => 50.00,
            'price_buy'       => 20.00,
            'cust_additional' => 5.00,
            'marketplace_id'  => $marketplaceId,
        ], $override);
    });

    // --- POST /api/v1/check-validate-product (preview, não persiste) ---

    it('calcula o preview de precificação sem persistir', function () {
        $response = $this->postJson('/api/v1/check-validate-product', ($this->payload)());

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // taxa = 50*20% + 4 = 14; lucro = 50 - 20 - 5 - 14 = 11
        expect((float) $response->json('data.profit_amount'))->toBe(11.0)
            ->and((float) $response->json('data.fee_total'))->toBe(14.0);

        $this->assertDatabaseCount('validate_products', 0);
    });

    it('valida os campos obrigatórios no preview com 422', function () {
        $this->postJson('/api/v1/check-validate-product', ($this->payload)(['price_sale' => null]))
            ->assertStatus(422);
    });

    // --- POST /api/v1/validate-product (persiste o snapshot) ---

    it('cria um validate product com o snapshot calculado', function () {
        $response = $this->postJson('/api/v1/validate-product', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        expect((float) $response->json('data.profit_amount'))->toBe(11.0);

        $this->assertDatabaseHas('validate_products', [
            'company_id' => $this->company->id,
            'name'       => 'Capa de Celular',
        ]);
    });

    it('exibe um validate product escopado à empresa do usuário', function () {
        $response = $this->postJson('/api/v1/validate-product', ($this->payload)());
        $id = $response->json('data.id');

        $response = $this->getJson("/api/v1/validate-product/{$id}")->assertStatus(200);

        expect((float) $response->json('data.profit_amount'))->toBe(11.0);
    });

    it('não encontra validate product de outra empresa', function () {
        $otherCompanyId = DB::table('companies')->insertGetId([
            'name' => 'Outra Empresa', 'status_id' => 1,
        ]);
        $otherUser = User::factory()->create();

        // ValidateProductFactory não tem defaults: o estado precisa ser explícito
        $other = ValidateProduct::factory()->create([
            'company_id'      => $otherCompanyId,
            'user_id'         => $otherUser->id,
            'name'            => 'Produto de outra empresa',
            'price_sale'      => 10,
            'price_buy'       => 5,
            'profit_amount'   => 5,
            'profit_margin'   => 50,
            'breakeven_roas'  => 2,
        ]);

        $this->getJson("/api/v1/validate-product/{$other->id}")->assertStatus(404);
    });

    it('lista validate products paginados', function () {
        $this->postJson('/api/v1/validate-product', ($this->payload)());

        $this->getJson('/api/v1/validate-product')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });

    it('exclui um validate product', function () {
        $id = $this->postJson('/api/v1/validate-product', ($this->payload)())->json('data.id');

        $this->deleteJson("/api/v1/validate-product/{$id}")->assertStatus(200);

        $this->assertDatabaseMissing('validate_products', ['id' => $id]);
    });
});
