<?php

namespace Tests\Feature\Http\Business;

use App\Models\Business\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('SupplierController', function () {

    beforeEach(function () {
        // SupplierFactory sorteia status_id entre 1 e 3 (rand(1, 3))
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
            ['id' => 3, 'name' => 'Pendente'],
        ]);

        // fornecedores não têm company_id (catálogo global): basta um usuário autenticado
        actingAsJwt();

        $this->payload = fn (array $override = []) => array_merge([
            'name'      => 'Fornecedor LTDA',
            'cnpj'      => '12345678000199',
            'email'     => 'fornecedor@example.com',
            'status_id' => 1,
        ], $override);
    });

    it('creates a supplier', function () {
        $response = $this->postJson('/api/v1/supplier', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Fornecedor LTDA');

        $this->assertDatabaseHas('suppliers', ['email' => 'fornecedor@example.com']);
    });

    it('rejects duplicate cnpj with 422', function () {
        Supplier::factory()->create(['cnpj' => '12345678000199']);

        $this->postJson('/api/v1/supplier', ($this->payload)(['email' => 'outro@example.com']))
            ->assertStatus(422);
    });

    it('rejects duplicate email with 422', function () {
        Supplier::factory()->create(['email' => 'fornecedor@example.com']);

        $this->postJson('/api/v1/supplier', ($this->payload)(['cnpj' => '99999999000199']))
            ->assertStatus(422);
    });

    it('validates required fields with 422', function () {
        $this->postJson('/api/v1/supplier', ($this->payload)(['name' => null]))
            ->assertStatus(422);
    });

    it('shows a supplier', function () {
        $supplier = Supplier::factory()->create();

        $this->getJson("/api/v1/supplier/{$supplier->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id);
    });

    it('updates a supplier', function () {
        $supplier = Supplier::factory()->create(['name' => 'Nome Antigo']);

        $this->putJson("/api/v1/supplier/{$supplier->id}", ['name' => 'Nome Novo'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Novo');
    });

    it('deletes a supplier', function () {
        $supplier = Supplier::factory()->create();

        $this->deleteJson("/api/v1/supplier/{$supplier->id}")->assertStatus(200);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    });

    it('lists suppliers paginated', function () {
        Supplier::factory()->count(2)->create();

        $this->getJson('/api/v1/supplier')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
