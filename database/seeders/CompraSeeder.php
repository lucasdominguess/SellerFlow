<?php

namespace Database\Seeders;

use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseItem;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        Purchase::factory()->count(20)->create()->each(function (Purchase $compra) {
            PurchaseItem::factory()->count(rand(1, 5))->create(['compra_id' => $compra->id]);
        });
    }
}
