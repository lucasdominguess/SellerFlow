<?php

namespace Database\Seeders;

use App\Models\ListSuspended\CategoriaFinanceira;
use Illuminate\Database\Seeder;

class CategoriaFinanceiraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoriaFinanceira::factory()->createMany(
            [
                [
                    'name' => 'ENTRADA'
                ],
                [
                    'name' => 'SAIDA'
                ],
            ]
        );
    }
}
