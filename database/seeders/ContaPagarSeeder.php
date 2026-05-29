<?php

namespace Database\Seeders;

use App\Models\Financial\ContaPagar;
use Illuminate\Database\Seeder;

class ContaPagarSeeder extends Seeder
{
    public function run(): void
    {
        ContaPagar::factory()->count(40)->create();
    }
}
