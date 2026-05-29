<?php

namespace Database\Seeders;

use App\Models\Accout\CompanyUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyUser::factory(10)->create();
    }
}
