<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // StatusSeeder::class,       // status (sem FK)
            // RoleSeeder::class,         // roles (sem FK)
            // MarketPlaceSeeder::class,  // market_places → status
            CompanySeeder::class,      // companies → status
            StoreSeeder::class,        // stores → status, marketplace, company
            UserSeeder::class,         // users → status
            UserStoreSeeder::class,    // user_stores → users, stores, roles, status
        ]);
    }
}
