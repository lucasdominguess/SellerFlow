<?php

namespace Tests\Feature\Http\Accout;

use App\Models\ListSuspended\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('CompanyController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);

        actingAsJwt();

        $this->payload = fn (array $override = []) => array_merge([
            'name'      => 'Minha Empresa LTDA',
            'cnpj'      => '11444777000161', // CNPJ válido (dígitos verificadores corretos)
            'status_id' => 1,
        ], $override);
    });

    it('creates a company', function () {
        $response = $this->postJson('/api/v1/company', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Minha Empresa LTDA');

        $this->assertDatabaseHas('companies', ['cnpj' => '11444777000161']);
    });

    it('rejects an invalid cnpj with 422', function () {
        $this->postJson('/api/v1/company', ($this->payload)(['cnpj' => '11111111111111']))
            ->assertStatus(422);
    });

    it('rejects duplicate cnpj with 422', function () {
        Company::factory()->create(['cnpj' => '11444777000161']);

        $this->postJson('/api/v1/company', ($this->payload)())
            ->assertStatus(422);
    });

    it('validates required fields with 422', function () {
        $this->postJson('/api/v1/company', ($this->payload)(['name' => null]))
            ->assertStatus(422);
    });

    it('shows a company', function () {
        $company = Company::factory()->create();

        $this->getJson("/api/v1/company/{$company->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $company->id);
    });

    it('updates only the provided field, preserving status_id on partial update', function () {
        $company = Company::factory()->create(['name' => 'Nome Antigo', 'status_id' => 1]);

        $this->putJson("/api/v1/company/{$company->id}", ['name' => 'Nome Novo'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Novo')
            ->assertJsonPath('data.status_id', 1);

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'Nome Novo', 'status_id' => 1]);
    });

    it('deletes a company', function () {
        $company = Company::factory()->create();

        $this->deleteJson("/api/v1/company/{$company->id}")->assertStatus(200);

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    });

    it('lists companies paginated', function () {
        Company::factory()->count(2)->create();

        $this->getJson('/api/v1/company')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
