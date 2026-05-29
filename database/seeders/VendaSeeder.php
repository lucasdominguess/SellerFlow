<?php

namespace Database\Seeders;

use App\Models\Sales\Venda;
use App\Models\Sales\VendaItem;
use Illuminate\Database\Seeder;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        Venda::factory()->count(30)->create()->each(function (Venda $venda) {
            VendaItem::factory()->count(rand(1, 4))->create(['venda_id' => $venda->id]);
        });
    }
}
