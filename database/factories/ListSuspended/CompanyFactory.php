<?php

namespace Database\Factories\ListSuspended;

use App\Models\ListSuspended\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        return [
            'name'        => $faker->company(),
            'cnpj'        => $faker->cnpj(false),
            'description' => $faker->optional(0.7)->sentence(),
            'status_id'   => 1, // Ativo
        ];
    }
}
