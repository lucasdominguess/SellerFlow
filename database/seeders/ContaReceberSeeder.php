<?php

namespace Database\Seeders;

use App\Models\Financial\ContaReceber;
use Illuminate\Database\Seeder;

class ContaReceberSeeder extends Seeder
{
    public function run(): void
    {
        ContaReceber::factory()->count(40)->create();
    }
}
