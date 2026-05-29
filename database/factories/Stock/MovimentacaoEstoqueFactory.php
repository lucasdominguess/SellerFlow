<?php

namespace Database\Factories\Stock;

use App\Models\Stock\MovimentacaoEstoque;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimentacaoEstoque>
 */
class MovimentacaoEstoqueFactory extends Factory
{
    public function definition(): array
    {
        $tipo       = $this->faker->randomElement(['entrada', 'saida', 'ajuste']);
        $origemTipo = match ($tipo) {
            'entrada' => 'compra',
            'saida'   => 'venda',
            'ajuste'  => 'ajuste_manual',
        };

        return [
            'company_id'  => 1,
            'product_id'  => $this->faker->numberBetween(1, 50),
            'user_id'     => 1,
            'tipo'        => $tipo,
            'quantidade'  => $this->faker->numberBetween(1, 50),
            'origem_tipo' => $origemTipo,
            'origem_id'   => $this->faker->numberBetween(1, 30),
            'observacao'  => $this->faker->optional(0.5)->sentence(),
        ];
    }
}
