<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\ListSuspended\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Lunnaire',
            'cnpj' => '00.000.000/0001-00',
            'description' => null,
            'status_id' => Status::ACTIVE->value,

        ]);
        Company::factory()->count(5)->create();
    }
}
