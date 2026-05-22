<?php

namespace Database\Seeders;

use App\Models\Business\Fornecedor;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Fornecedor::factory()->count(20)->create();
    }
}
