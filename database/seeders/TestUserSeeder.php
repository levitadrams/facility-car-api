<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário já existe
        if (User::where('email', 'teste@email.com')->exists()) {
            $this->command->info('Usuário de teste já existe!');
            return;
        }

        // Cria usuário de teste
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@email.com',
            'password' => Hash::make('12345678'),
        ]);

        $this->command->info('Usuário de teste criado com sucesso!');
        $this->command->info('Email: teste@email.com');
        $this->command->info('Senha: 12345678');
    }
}
