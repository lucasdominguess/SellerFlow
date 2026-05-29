<?php

namespace Database\Factories\Financial;

use App\Models\Financial\ContaPagar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContaPagar>
 */
class ContaPagarFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pendente', 'pago', 'atrasado']);

        return [
            'company_id'              => 1,
            'user_id'                 => 1,
            'categoria_financeira_id' => $this->faker->numberBetween(1, 2),
            'forma_pagamento_id'      => $this->faker->numberBetween(1, 5),
            'fornecedor_id'           => $this->faker->optional(0.7)->numberBetween(1, 20),
            'compra_id'               => null,
            'descricao'               => $this->faker->sentence(4),
            'valor'                   => $this->faker->randomFloat(2, 50, 2000),
            'vencimento'              => $this->faker->dateTimeBetween('-2 months', '+3 months')->format('Y-m-d'),
            'status'                  => $status,
            'data_pagamento'          => $status === 'pago' ? $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d') : null,
            'observacao'              => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
