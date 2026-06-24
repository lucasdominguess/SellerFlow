<?php

namespace Tests\Feature\Http\Accout;

use App\Models\Accout\Store;
use App\Models\Accout\User;
use App\Models\Accout\UserStore;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('UserStoreController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);
        DB::table('roles')->insert([['id' => 1, 'name' => 'admin'], ['id' => 2, 'name' => 'user']]);

        actingAsJwt();

        $company     = Company::factory()->create();
        $marketplace = MarketPlace::factory()->create();
        $this->targetUser = User::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $company->id, 'marketplace_id' => $marketplace->id]);

        $userId  = $this->targetUser->id;
        $storeId = $this->store->id;
        $this->payload = fn (array $override = []) => array_merge([
            'user_id'   => $userId,
            'store_id'  => $storeId,
            'role_id'   => 2,
            'status_id' => 1,
        ], $override);
    });

    it('cria um vínculo usuário-loja', function () {
        $response = $this->postJson('/api/v1/user-store', ($this->payload)());

        $response->assertStatus(201)->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_stores', [
            'user_id' => $this->targetUser->id, 'store_id' => $this->store->id, 'role_id' => 2,
        ]);
    });

    it('rejeita um vínculo usuário-loja duplicado com 422', function () {
        UserStore::create(($this->payload)());

        $this->postJson('/api/v1/user-store', ($this->payload)())->assertStatus(422);
    });

    it('valida os campos obrigatórios com 422', function () {
        $this->postJson('/api/v1/user-store', ($this->payload)(['user_id' => null]))
            ->assertStatus(422);
    });

    it('exibe um vínculo usuário-loja', function () {
        $link = UserStore::create(($this->payload)());

        $this->getJson("/api/v1/user-store/{$link->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $link->id);
    });

    it('atualiza apenas o role_id, preservando os demais campos no update parcial', function () {
        $link = UserStore::create(($this->payload)());

        $this->putJson("/api/v1/user-store/{$link->id}", ['role_id' => 1])
            ->assertStatus(200)
            ->assertJsonPath('data.role_id', 1);

        $this->assertDatabaseHas('user_stores', [
            'id' => $link->id, 'role_id' => 1, 'user_id' => $this->targetUser->id, 'store_id' => $this->store->id,
        ]);
    });

    it('exclui um vínculo usuário-loja', function () {
        $link = UserStore::create(($this->payload)());

        $this->deleteJson("/api/v1/user-store/{$link->id}")->assertStatus(200);

        $this->assertDatabaseMissing('user_stores', ['id' => $link->id]);
    });

    it('lista vínculos usuário-loja paginados', function () {
        UserStore::create(($this->payload)());

        $this->getJson('/api/v1/user-store')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
