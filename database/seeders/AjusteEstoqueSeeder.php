<?php

namespace Database\Seeders;

use App\Models\Stock\AjusteEstoque;
use App\Models\Stock\MovimentacaoEstoque;
use Illuminate\Database\Seeder;

class AjusteEstoqueSeeder extends Seeder
{
    public function run(): void
    {
        AjusteEstoque::factory()->count(20)->create()->each(function (AjusteEstoque $ajuste) {
            MovimentacaoEstoque::create([
                'product_id'  => $ajuste->product_id,
                'user_id'     => $ajuste->user_id,
                'tipo'        => 'ajuste',
                'quantidade'  => abs($ajuste->quantidade),
                'origem_tipo' => 'ajuste_manual',
                'origem_id'   => $ajuste->id,
                'observacao'  => $ajuste->observacao,
                'company_id'  => $ajuste->company_id,
            ]);
        });
    }
}
