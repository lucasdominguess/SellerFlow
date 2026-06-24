<?php

namespace Tests\Feature\Http\Finance;

use App\Models\Finance\AccountPayable;
use App\Models\ListSuspended\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('AccountPayableController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);

        $ctx = actingAsCompanyJwt();
        $this->company = $ctx['company'];

        // helper: cria uma conta a pagar da empresa autenticada (status/origem usam o default da coluna)
        $companyId = $this->company->id;
        $this->makePayable = fn (array $override = []) => AccountPayable::create(array_merge([
            'company_id' => $companyId,
            'valor'      => 150.00,
            'status'     => 'pendente',
            'origem_tipo' => 'ajuste_manual',
        ], $override));
    });

    it('cria uma conta a pagar com company_id vindo do token', function () {
        $response = $this->postJson('/api/v1/account-payable', [
            'valor'      => 350.00,
            'vencimento' => '2026-07-10',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.company_id', $this->company->id)
            ->assertJsonPath('data.status', 'pendente');

        $this->assertDatabaseHas('account_payables', ['company_id' => $this->company->id, 'valor' => 350.00]);
    });

    it('valida que valor é obrigatório com 422', function () {
        $this->postJson('/api/v1/account-payable', ['vencimento' => '2026-07-10'])
            ->assertStatus(422);
    });

    it('exibe uma conta a pagar', function () {
        $payable = ($this->makePayable)();

        $this->getJson("/api/v1/account-payable/{$payable->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $payable->id);
    });

    it('atualiza o status sem alterar os demais campos', function () {
        $payable = ($this->makePayable)(['observacao' => 'Pagamento fornecedor']);

        $this->putJson("/api/v1/account-payable/{$payable->id}", ['status' => 'concluido'])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'concluido')
            ->assertJsonPath('data.observacao', 'Pagamento fornecedor');

        $this->assertDatabaseHas('account_payables', ['id' => $payable->id, 'status' => 'concluido']);
    });

    it('exclui uma conta a pagar', function () {
        $payable = ($this->makePayable)();

        $this->deleteJson("/api/v1/account-payable/{$payable->id}")->assertStatus(200);

        $this->assertDatabaseMissing('account_payables', ['id' => $payable->id]);
    });

    it('não exibe conta a pagar de outra empresa', function () {
        $otherCompanyId = DB::table('companies')->insertGetId(['name' => 'Outra', 'status_id' => 1]);
        $other = AccountPayable::create([
            'company_id' => $otherCompanyId, 'valor' => 99, 'status' => 'pendente', 'origem_tipo' => 'ajuste_manual',
        ]);

        $this->getJson("/api/v1/account-payable/{$other->id}")->assertStatus(404);
    });

    it('lista contas a pagar paginadas', function () {
        ($this->makePayable)();

        $this->getJson('/api/v1/account-payable')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
