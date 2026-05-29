<?php

namespace Database\Factories\Accout;

use App\Models\Accout\CompanyUser;
use App\Models\Accout\User;
use App\Models\ListSuspended\Company;
use App\Models\ListSuspended\Role;
use App\Models\ListSuspended\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyUser>
 */
class CompanyUserFactory extends Factory
{
    protected $model = CompanyUser::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->value('id'),
            'user_id'    => User::inRandomOrder()->value('id'),
            'role_id'    => Role::inRandomOrder()->value('id'),
            'status_id'  => Status::inRandomOrder()->value('id'),
        ];
    }
}
