<?php

namespace Database\Factories\Sales;

use App\Models\Sales\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    public function definition(): array
    {
        $quantidade    = $this->faker->numberBetween(1, 10);
        $valorUnitario = $this->faker->randomFloat(2, 10, 200);

        return [
            'venda_id'       => 1,
            'product_id'     => $this->faker->numberBetween(1, 50),
            'quantidade'     => $quantidade,
            'valor_unitario' => $valorUnitario,
            'valor_total'    => round($quantidade * $valorUnitario, 2),
        ];
    }
}
