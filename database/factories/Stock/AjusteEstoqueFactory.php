<?php

namespace Database\Factories\Stock;

use App\Models\Stock\AjusteEstoque;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AjusteEstoque>
 */
class AjusteEstoqueFactory extends Factory
{
    public function definition(): array
    {
        $motivo     = $this->faker->randomElement(['perda', 'quebra', 'contagem_fisica', 'devolucao', 'outro']);
        // Devoluções e contagem física podem ser positivos; perdas/quebras são negativos
        $quantidade = in_array($motivo, ['devolucao', 'contagem_fisica'])
            ? $this->faker->numberBetween(1, 20)
            : -$this->faker->numberBetween(1, 5);

        return [
            'company_id'  => 1,
            'product_id'  => $this->faker->numberBetween(1, 50),
            'user_id'     => 1,
            'quantidade'  => $quantidade,
            'motivo'      => $motivo,
            'observacao'  => $this->faker->optional(0.5)->sentence(),
        ];
    }
}
