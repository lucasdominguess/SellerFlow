<?php

namespace Tests\Feature\Http\Finance;

use App\Models\Accout\CompanyUser;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Finance\AccountReceivable;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('AccountReceivableController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);
        DB::table('roles')->insert([['id' => 1, 'name' => 'admin']]);

        $this->company = Company::factory()->create();
        $this->user    = User::factory()->create(['status_id' => 1]);
        CompanyUser::factory()->create([
            'company_id' => $this->company->id, 'user_id' => $this->user->id, 'role_id' => 1, 'status_id' => 1,
        ]);

        $marketplace = MarketPlace::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id, 'marketplace_id' => $marketplace->id]);
        // a conta a receber exige store_id, que vem de storeIds() no JWT
        DB::table('user_stores')->insert([
            'user_id' => $this->user->id, 'store_id' => $this->store->id, 'role_id' => 1, 'status_id' => 1,
        ]);

        $token = auth('api')->login($this->user);
        $this->withHeader('Authorization', "Bearer {$token}");

        $companyId = $this->company->id;
        $storeId   = $this->store->id;
        $this->makeReceivable = fn (array $override = []) => AccountReceivable::create(array_merge([
            'company_id' => $companyId,
            'store_id'   => $storeId,
            'valor'      => 199.90,
            'status'     => 'pendente',
            'origem_tipo' => 'ajuste_manual',
        ], $override));
    });

    it('cria uma conta a receber com company_id e store_id vindos do token', function () {
        $response = $this->postJson('/api/v1/account-receivable', [
            'valor'                => 250.00,
            'previsao_recebimento' => '2026-07-15',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.company_id', $this->company->id)
            ->assertJsonPath('data.store_id', $this->store->id)
            ->assertJsonPath('data.status', 'pendente');

        $this->assertDatabaseHas('account_receivables', [
            'company_id' => $this->company->id, 'store_id' => $this->store->id, 'valor' => 250.00,
        ]);
    });

    it('valida que valor é obrigatório com 422', function () {
        $this->postJson('/api/v1/account-receivable', ['previsao_recebimento' => '2026-07-15'])
            ->assertStatus(422);
    });

    it('exibe uma conta a receber', function () {
        $receivable = ($this->makeReceivable)();

        $this->getJson("/api/v1/account-receivable/{$receivable->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $receivable->id);
    });

    it('atualiza o status sem alterar os demais campos', function () {
        $receivable = ($this->makeReceivable)(['observacao' => 'Repasse marketplace']);

        $this->putJson("/api/v1/account-receivable/{$receivable->id}", ['status' => 'concluido'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'concluido')
            ->assertJsonPath('data.observacao', 'Repasse marketplace');

        $this->assertDatabaseHas('account_receivables', ['id' => $receivable->id, 'status' => 'concluido']);
    });

    it('exclui uma conta a receber', function () {
        $receivable = ($this->makeReceivable)();

        $this->deleteJson("/api/v1/account-receivable/{$receivable->id}")->assertStatus(200);

        $this->assertDatabaseMissing('account_receivables', ['id' => $receivable->id]);
    });

    it('não exibe conta a receber de outra empresa', function () {
        $otherCompanyId = DB::table('companies')->insertGetId(['name' => 'Outra', 'status_id' => 1]);
        $other = AccountReceivable::create([
            'company_id' => $otherCompanyId, 'store_id' => $this->store->id, 'valor' => 99,
            'status' => 'pendente', 'origem_tipo' => 'ajuste_manual',
        ]);

        $this->getJson("/api/v1/account-receivable/{$other->id}")->assertStatus(404);
    });

    it('lista contas a receber paginadas', function () {
        ($this->makeReceivable)();

        $this->getJson('/api/v1/account-receivable')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
