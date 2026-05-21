<?php

namespace Database\Seeders;

use App\Models\ListSuspended\MarketPlace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //inserir varios dados de uma vez na tabela

        MarketPlace::factory()->createMany(
            [

                [
                    'name' => 'Amazon',
                    'taxa_percentual' => 16.0,
                    'taxa_fixa' => 4.0,
                    'status_id' => 2,
                ],
                [
                    'name' => 'Mercado Livre',
                    'taxa_percentual' => 16.0,
                    'taxa_fixa' => 4.0,
                    'status_id' => 1,
                ],
                [
                    'name' => 'Shopee',
                    'taxa_percentual' => 16.0,
                    'taxa_fixa' => 4.0,
                    'status_id' => 1,
                ],
                [
                    'name' => 'Tiktok Shop',
                    'taxa_percentual' => 16.0,
                    'taxa_fixa' => 4.0,
                    'status_id' => 1,
                ]
            ]
        );
    }
}
