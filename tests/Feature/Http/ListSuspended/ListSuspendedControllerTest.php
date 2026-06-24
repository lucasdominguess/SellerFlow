<?php

namespace Tests\Feature\Http\ListSuspended;

use App\Models\Business\Product;
use App\Models\Business\Supplier;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('ListSuspendedController', function () {

    beforeEach(function () {
        // status 1-3: Supplier/Product factories sorteiam status_id entre 1 e 3
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);

        // uma linha de cada lista auxiliar
        $supplier = Supplier::factory()->create();
        Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);
        MarketPlace::factory()->create();
        Company::factory()->create();
        DB::table('financial_categories')->insert(['id' => 1, 'name' => 'Entrada']);
        DB::table('payment_methods')->insert(['id' => 1, 'name' => 'PIX']);
    });

    // rota pública: cada params válido devolve a lista correspondente
    it('retorna a lista para um valor de params válido', function (string $params) {
        $this->getJson("/api/v1/list?params={$params}")
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    })->with([
        'categoria-financeira',
        'fornecedor',
        'forma-pagamento',
        'marketplace',
        'produto',
        'company',
    ]);

    it('exige o campo params com 422', function () {
        $this->getJson('/api/v1/list')->assertStatus(422);
    });

    it('rejeita um valor de params inválido com 422', function () {
        $this->getJson('/api/v1/list?params=inexistente')->assertStatus(422);
    });

    it('filtra por status_id', function () {
        // outra marketplace inativa não deve aparecer ao filtrar por status 1
        MarketPlace::factory()->create(['status_id' => 2]);

        // a marketplace semeada no beforeEach é ativa (factory usa status_id=1)
        $this->getJson('/api/v1/list?params=marketplace&status_id=1')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status_id', 1);
    });

    it('filtra por name', function () {
        // o filtro por nome usa ilike (exclusivo do Postgres)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('ilike só existe no Postgres.');
        }

        $alvo = Supplier::factory()->create(['name' => 'Fornecedor Alvo XYZ']);

        $response = $this->getJson('/api/v1/list?params=fornecedor&name=Alvo XYZ')
            ->assertStatus(200);

        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.id'))->toBe($alvo->id);
    });
});
