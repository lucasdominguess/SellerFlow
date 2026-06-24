<?php

namespace Tests\Feature\Http\Accout;

use App\Models\Accout\Store;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('StoreController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);

        actingAsJwt();

        $this->company     = Company::factory()->create();
        $this->marketplace = MarketPlace::factory()->create();

        $marketplaceId = $this->marketplace->id;
        $companyId     = $this->company->id;
        $this->payload = fn (array $override = []) => array_merge([
            'name'           => 'Minha Loja',
            'status_id'      => 1,
            'marketplace_id' => $marketplaceId,
            'company_id'     => $companyId,
        ], $override);
    });

    it('cria uma loja', function () {
        $response = $this->postJson('/api/v1/store', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Minha Loja');

        $this->assertDatabaseHas('stores', ['name' => 'Minha Loja', 'company_id' => $this->company->id]);
    });

    it('cria uma loja sem company_id (company_id é opcional)', function () {
        $payload = ($this->payload)();
        unset($payload['company_id']);

        $this->postJson('/api/v1/store', $payload)
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Minha Loja');

        $this->assertDatabaseHas('stores', ['name' => 'Minha Loja', 'company_id' => null]);
    });

    it('valida os campos obrigatórios com 422', function () {
        $this->postJson('/api/v1/store', ($this->payload)(['name' => null]))
            ->assertStatus(422);
    });

    it('exibe uma loja', function () {
        $store = Store::factory()->create([
            'company_id' => $this->company->id, 'marketplace_id' => $this->marketplace->id,
        ]);

        $this->getJson("/api/v1/store/{$store->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $store->id);
    });

    it('atualiza apenas o nome, preservando os demais campos no update parcial', function () {
        $store = Store::factory()->create([
            'name' => 'Nome Antigo', 'status_id' => 1,
            'company_id' => $this->company->id, 'marketplace_id' => $this->marketplace->id,
        ]);

        $this->putJson("/api/v1/store/{$store->id}", ['name' => 'Nome Novo'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Novo')
            ->assertJsonPath('data.marketplace.id', $this->marketplace->id);

        $this->assertDatabaseHas('stores', [
            'id' => $store->id, 'name' => 'Nome Novo', 'marketplace_id' => $this->marketplace->id,
        ]);
    });

    it('exclui uma loja', function () {
        $store = Store::factory()->create([
            'company_id' => $this->company->id, 'marketplace_id' => $this->marketplace->id,
        ]);

        $this->deleteJson("/api/v1/store/{$store->id}")->assertStatus(200);

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    });

    it('lista lojas paginadas', function () {
        Store::factory()->count(2)->create([
            'company_id' => $this->company->id, 'marketplace_id' => $this->marketplace->id,
        ]);

        $this->getJson('/api/v1/store')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
