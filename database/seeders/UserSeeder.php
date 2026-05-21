<?php

namespace Database\Seeders;

use App\Models\Accout\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário fixo para desenvolvimento/testes
        User::factory()->create([
            'name'      => 'Administrador',
            'email'     => 'admin@sellerflow.com',
            'password'  => bcrypt('password'),
            'status_id' => 2,
        ]);

        User::factory()->count(5)->create();
    }
}
