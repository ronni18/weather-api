<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'language' => 'es',
        ]);
        $adminUser->assignRole('admin');            

        $usuarios = [
            [
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password123'),
                'language' => 'es',
            ],
            [
                'name' => 'Ana Torres',
                'email' => 'ana@example.com',
                'password' => Hash::make('password123'),
                'language' => 'es',

            ],
            [
                'name' => 'Carlos Gómez',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password123'),
                'language' => 'es',

            ],
        ];

        foreach ($usuarios as $usuario) {
            $nuevoUsuario = User::create($usuario);
            $nuevoUsuario->assignRole('consultant');            
        }

        $this->command->info('Usuarios de prueba insertados correctamente.');
    }
}
