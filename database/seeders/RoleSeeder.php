<?php

namespace Database\Seeders;

use App\Models\ListSuspended\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Role::factory()->createMany([
            ['name' => 'Admin'],
            ['name' => 'User'],
        ]);
    }
}
