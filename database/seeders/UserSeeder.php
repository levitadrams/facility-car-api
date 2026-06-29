<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin123'),
            'phone' => '11999999999',
            'cpf' => '12345678901',
        ]);

        // MOTORISTA
        User::create([
            'name' => 'Jefferson',
            'email' => 'jefferson@gmail.com',
            'password' => Hash::make('Admin123'),
            'phone' => '11988888888',
            'cpf' => '12345678902',
        ]);
    }
}

