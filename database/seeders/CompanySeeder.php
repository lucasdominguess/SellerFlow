<?php

namespace Database\Seeders;

use App\Models\ListSuspended\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::factory()->count(5)->create();
    }
}
