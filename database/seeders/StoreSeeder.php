<?php

namespace Database\Seeders;

use App\Models\Accout\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::factory()->count(10)->create();
    }
}
