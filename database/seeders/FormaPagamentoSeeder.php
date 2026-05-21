<?php

namespace Database\Seeders;

use App\Models\ListSuspended\FormaPagamento;
use Illuminate\Database\Seeder;

class FormaPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       FormaPagamento::factory()->createMany([
        [
        'name'=>'DEBITO'
        ],
        [
        'name'=>'CREDITO'
        ],
        [
        'name'=>'PIX'
        ],
        [
        'name'=>'PARCELADO'
        ],
        [
        'name'=>'DINHEIRO'
        ],
       ],
       );
    }
}
