<?php

namespace Tests\Feature;

use App\Models\Accout\CompanyUser;
use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use App\Models\Sales\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('CompanyScope (isolamento por empresa)', function () {

    beforeEach(function () {
        // FKs base: status (companies=1, users=2) e roles (CompanyUser.role_id)
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);
        DB::table('roles')->insert(['id' => 1, 'name' => 'admin']);

        $marketplace = MarketPlace::factory()->create();

        $this->companyA = Company::factory()->create();
        $this->companyB = Company::factory()->create();

        // Usuário autenticado pertence apenas à empresa A → JWT carrega só company A
        $this->user = User::factory()->create();
        CompanyUser::factory()->create([
            'company_id' => $this->companyA->id,
            'user_id'    => $this->user->id,
            'role_id'    => 1,
            'status_id'  => 1,
        ]);

        $storeA = Store::factory()->create(['company_id' => $this->companyA->id, 'marketplace_id' => $marketplace->id]);
        $storeB = Store::factory()->create(['company_id' => $this->companyB->id, 'marketplace_id' => $marketplace->id]);

        $this->saleA = Sale::factory()->create([
            'company_id' => $this->companyA->id, 'store_id' => $storeA->id,
            'market_place_id' => $marketplace->id, 'user_id' => $this->user->id,
        ]);
        $this->saleB = Sale::factory()->create([
            'company_id' => $this->companyB->id, 'store_id' => $storeB->id,
            'market_place_id' => $marketplace->id, 'user_id' => $this->user->id,
        ]);
    });

    // Autenticado na empresa A, a listagem só enxerga vendas da empresa A.
    it('lista apenas registros da empresa do usuário autenticado', function () {
        auth('api')->login($this->user);

        $ids = Sale::pluck('id');

        expect($ids)->toContain($this->saleA->id)
            ->and($ids)->not->toContain($this->saleB->id);
    });

    // Buscar por ID um registro de outra empresa não retorna nada (equivale ao 404 no route-model binding).
    it('não encontra registro de outra empresa por ID', function () {
        auth('api')->login($this->user);

        expect(Sale::find($this->saleB->id))->toBeNull()
            ->and(Sale::find($this->saleA->id))->not->toBeNull();
    });

    // Sem token (commands, seeders, jobs) o escopo é desligado: acesso total.
    it('não aplica escopo quando não há usuário autenticado', function () {
        expect(Sale::count())->toBe(2);
    });
});
