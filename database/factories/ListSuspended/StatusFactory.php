<?php

namespace Database\Factories\ListSuspended;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
