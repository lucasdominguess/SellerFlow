<?php

namespace Database\Seeders;

use App\Models\Purchases\Compra;
use App\Models\Purchases\CompraItem;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        Compra::factory()->count(20)->create()->each(function (Compra $compra) {
            CompraItem::factory()->count(rand(1, 5))->create(['compra_id' => $compra->id]);
        });
    }
}
