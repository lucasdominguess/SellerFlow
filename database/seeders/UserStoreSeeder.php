<?php

namespace Database\Seeders;

use App\Models\Accout\Store;
use App\Models\Accout\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStoreSeeder extends Seeder
{
    public function run(): void
    {
        $users  = User::all();
        $stores = Store::all();

        $users->each(function (User $user) use ($stores) {
            // Cada usuário recebe 1 ou 2 lojas aleatórias
            $stores->random(min(rand(1, 2), $stores->count()))
                ->each(function (Store $store) use ($user) {
                    $alreadyLinked = DB::table('user_stores')
                        ->where('user_id', $user->id)
                        ->where('store_id', $store->id)
                        ->exists();

                    if ($alreadyLinked) {
                        return;
                    }

                    DB::table('user_stores')->insert([
                        'user_id'  => $user->id,
                        'store_id' => $store->id,
                        // primeiro usuário criado é Admin, demais são User
                        'role_id'   => $user->id === 1 ? 1 : 2,
                        'status_id' => 1,
                    ]);
                });
        });
    }
}
