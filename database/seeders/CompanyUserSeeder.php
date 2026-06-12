<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Enums\Status;
use App\Models\Accout\CompanyUser;

use Illuminate\Database\Seeder;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyUser::create([
            'company_id' => 1,
            'user_id' => 1,
            'role_id' => Roles::ADMIN->value,
            'status_id' =>Status::ACTIVE->value
        ]);
        CompanyUser::factory(10)->create();
    }
}
