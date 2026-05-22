<?php

namespace Database\Factories\Business;

use App\Models\Business\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'name' => $this->faker->word(),
            'marca' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'price_unit' => $this->faker->randomFloat(2, 10, 100),
            'price_box' => $this->faker->randomFloat(2, 100, 500),
            'status_id' => rand(1, 3),
            'fornecedor_id' => rand(1, 20),
            'path_image' => null
        ];
    }
}
