<?php

namespace Database\Factories\Accout;

use App\Models\Accout\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        return [
            'name'      => $faker->name(),
            'email'     => $faker->unique()->safeEmail(),
            'password'  => bcrypt('password'),
            'status_id' => 2, // Ativo
        ];
    }
}
