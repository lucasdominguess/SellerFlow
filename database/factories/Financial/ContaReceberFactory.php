<?php

namespace Database\Factories\Financial;

use App\Models\Financial\ContaReceber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContaReceber>
 */
class ContaReceberFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pendente', 'recebido', 'atrasado']);
        $bruto  = $this->faker->randomFloat(2, 30, 500);
        $taxa   = round($bruto * $this->faker->randomFloat(2, 0.10, 0.20), 2);
        $frete  = $this->faker->randomFloat(2, 0, 30);

        return [
            'company_id'            => 1,
            'user_id'               => 1,
            'market_place_id'       => $this->faker->numberBetween(1, 3),
            'venda_id'              => null,
            'descricao'             => 'Repasse pedido #' . $this->faker->numerify('##########'),
            'valor_bruto'           => $bruto,
            'taxa_marketplace'      => $taxa,
            'valor_frete'           => $frete,
            'valor_liquido'         => round($bruto - $taxa - $frete, 2),
            'previsao_recebimento'  => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'data_recebimento_real' => $status === 'recebido' ? $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d') : null,
            'status'                => $status,
            'observacao'            => null,
        ];
    }
}
