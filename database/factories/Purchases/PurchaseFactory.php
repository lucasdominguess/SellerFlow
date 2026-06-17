<?php

namespace Database\Factories\Purchases;

use App\Enums\TransactionStatus;
use App\Models\Purchases\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id'        => 1,
            'store_id'          => 1,
            'fornecedor_id'     => $this->faker->numberBetween(1, 20),
            'user_id'           => 1,
            'forma_pagamento_id'=> $this->faker->numberBetween(1, 5),
            'status'            => TransactionStatus::PENDING->value,
            'numero_nota'       => $this->faker->optional(0.7)->numerify('NF-#####'),
            'data_compra'       => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'valor_total'       => $this->faker->randomFloat(2, 100, 5000),
            'numero_parcelas'   => $this->faker->randomElement([1, 2, 3]),
            'observacao'        => null,
        ];
    }
}
