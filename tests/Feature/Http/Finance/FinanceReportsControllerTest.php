<?php

namespace Tests\Feature\Http\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('Finance reports (cash-flow & dashboard)', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);

        // ambos os relatórios escopam por company_id do JWT
        actingAsCompanyJwt();
    });

    // --- GET /api/v1/finance/cash-flow ---

    it('valida o intervalo de datas obrigatório no cash-flow com 422', function () {
        // start_date e end_date são obrigatórios; validação roda antes da query
        $this->getJson('/api/v1/finance/cash-flow')->assertStatus(422);
    });

    it('retorna o relatório de fluxo de caixa realizado', function () {
        // a agregação usa date_trunc (exclusivo do Postgres)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('date_trunc só existe no Postgres.');
        }

        $this->getJson('/api/v1/finance/cash-flow?start_date=2026-06-01&end_date=2026-06-30')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['granularity', 'start_date', 'end_date', 'summary', 'periods']]);
    });

    // --- GET /api/v1/finance/dashboard ---

    it('valida o intervalo de datas no dashboard com 422', function () {
        // end_date deve ser >= start_date; validação roda antes do service
        $this->getJson('/api/v1/finance/dashboard?start_date=2026-06-30&end_date=2026-06-01')
            ->assertStatus(422);
    });

    it('retorna o resumo do dashboard', function () {
        // total_investido lê a stock_investment_view (exclusiva do Postgres)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('stock_investment_view só existe no Postgres.');
        }

        $this->getJson('/api/v1/finance/dashboard')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['periodo', 'vendas', 'compras', 'financeiro', 'estoque', 'top_produtos'],
            ]);
    });
});
