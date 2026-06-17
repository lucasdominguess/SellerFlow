<?php

namespace Database\Seeders;

use App\Models\Business\Supplier;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Supplier::factory()->count(20)->create();
    }
}
