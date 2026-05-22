<?php

namespace Database\Factories\Business;

use App\Models\Business\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fornecedor>
 */
class FornecedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'responsavel' => $this->faker->name(),
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'link_catalog' => $this->faker->url(),
            'description' => $this->faker->sentence(),
            'status_id' => rand(1, 3),
        ];
    }
}
