<?php

namespace Database\Seeders;

use App\Models\ListSuspended\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::factory()->createMany([
            ['name' => 'Ativo'],
            ['name' => 'Inativo'],
            ['name' => 'Pendente'],
        ]);
    }
}
