<?php

namespace Database\Factories\Purchases;

use App\Models\Purchases\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    public function definition(): array
    {
        $quantidade    = $this->faker->numberBetween(1, 50);
        $valorUnitario = $this->faker->randomFloat(2, 5, 300);

        return [
            'compra_id'      => 1,
            'product_id'     => $this->faker->numberBetween(1, 50),
            'quantidade'     => $quantidade,
            'valor_unitario' => $valorUnitario,
            'valor_total'    => round($quantidade * $valorUnitario, 2),
        ];
    }
}
