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

    it('creates a user-store link', function () {
        $response = $this->postJson('/api/v1/user-store', ($this->payload)());

        $response->assertStatus(201)->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_stores', [
            'user_id' => $this->targetUser->id, 'store_id' => $this->store->id, 'role_id' => 2,
        ]);
    });

    it('rejects a duplicate user-store link with 422', function () {
        UserStore::create(($this->payload)());

        $this->postJson('/api/v1/user-store', ($this->payload)())->assertStatus(422);
    });

    it('validates required fields with 422', function () {
        $this->postJson('/api/v1/user-store', ($this->payload)(['user_id' => null]))
            ->assertStatus(422);
    });

    it('shows a user-store link', function () {
        $link = UserStore::create(($this->payload)());

        $this->getJson("/api/v1/user-store/{$link->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $link->id);
    });

    it('updates only role_id, preserving the other fields on partial update', function () {
        $link = UserStore::create(($this->payload)());

        $this->putJson("/api/v1/user-store/{$link->id}", ['role_id' => 1])
            ->assertStatus(200)
            ->assertJsonPath('data.role_id', 1);

        $this->assertDatabaseHas('user_stores', [
            'id' => $link->id, 'role_id' => 1, 'user_id' => $this->targetUser->id, 'store_id' => $this->store->id,
        ]);
    });

    it('deletes a user-store link', function () {
        $link = UserStore::create(($this->payload)());

        $this->deleteJson("/api/v1/user-store/{$link->id}")->assertStatus(200);

        $this->assertDatabaseMissing('user_stores', ['id' => $link->id]);
    });

    it('lists user-store links paginated', function () {
        UserStore::create(($this->payload)());

        $this->getJson('/api/v1/user-store')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
