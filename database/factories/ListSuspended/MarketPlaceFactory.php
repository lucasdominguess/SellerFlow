<?php

namespace Database\Factories\ListSuspended;

use App\Models\ListSuspended\MarketPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketPlace>
 */
class MarketPlaceFactory extends Factory
{
    protected $model = MarketPlace::class;

    public function definition(): array
    {
        return [
            'name'            => $this->faker->unique()->company(),
            'description'     => $this->faker->optional()->sentence(),
            'taxa_percentual'  => $this->faker->randomFloat(2, 5, 25),
            'taxa_fixa'        => $this->faker->randomFloat(2, 1, 10),
            'status_id'        => 1,
        ];
    }
}
