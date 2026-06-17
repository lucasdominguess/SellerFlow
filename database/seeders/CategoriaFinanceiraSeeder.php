<?php

namespace Database\Seeders;

use App\Models\ListSuspended\FinancialCategory;
use Illuminate\Database\Seeder;

class CategoriaFinanceiraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialCategory::factory()->createMany(
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
