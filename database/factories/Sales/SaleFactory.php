<?php

namespace Database\Factories\Sales;

use App\Enums\TransactionStatus;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        $bruto = $this->faker->randomFloat(2, 30, 500);
        $taxa  = round($bruto * $this->faker->randomFloat(2, 0.10, 0.20), 2);
        $frete = $this->faker->randomFloat(2, 0, 30);

        return [
            'company_id'            => 1,
            'store_id'              => 1,
            'market_place_id'       => $this->faker->numberBetween(1, 3),
            'user_id'               => 1,
            'numero_pedido'         => $this->faker->unique()->numerify('##########'),
            'data_venda'            => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'valor_bruto'           => $bruto,
            'taxa_marketplace'      => $taxa,
            'valor_frete'           => $frete,
            'valor_liquido'         => round($bruto - $taxa - $frete, 2),
            'data_previsao_repasse' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'status'                => TransactionStatus::PENDING->value,
            'observacao'            => null,
        ];
    }
}
