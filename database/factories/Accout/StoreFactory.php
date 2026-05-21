<?php

namespace Database\Factories\Accout;

use App\Models\Accout\Store;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\MarketPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        return [
            'name'           => $faker->company(),
            'email'          => $faker->optional(0.8)->companyEmail(),
            'description'    => $faker->optional(0.6)->sentence(),
            'status_id'      => 1, // Ativo
            'marketplace_id' => MarketPlace::inRandomOrder()->value('id'),
            'company_id'     => Company::inRandomOrder()->value('id'),
        ];
    }
}
