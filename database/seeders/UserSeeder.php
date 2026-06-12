<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Accout\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuário fixo para desenvolvimento/testes
        User::factory()->create([
            'name'      => 'Lucas Domingues',
            'email'     => 'lunnairestore@gmail.com',
            'password'  => bcrypt('password'),
            'status_id' => Status::ACTIVE->value,
        ]);

        User::factory()->count(5)->create();
    }
}
