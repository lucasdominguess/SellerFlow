<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Accout\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::create([
            'name' => 'Lunnaire',
            'email' => 'lunnairestore@gmail.com',
            'marketplace_id' => 3,
            'description' => null,
            'company_id' => 1,
            'status_id' => Status::ACTIVE->value
        ]);
        Store::factory()->count(10)->create();
    }
}
