<?php

namespace Database\Seeders;

use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Database\Seeder;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        Sale::factory()->count(30)->create()->each(function (Sale $venda) {
            SaleItem::factory()->count(rand(1, 4))->create(['venda_id' => $venda->id]);
        });
    }
}
