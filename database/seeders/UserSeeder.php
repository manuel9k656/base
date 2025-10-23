<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@yopmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);

        // Usuario de prueba
        User::create([
            'name' => 'Manuel Martinez',
            'email' => 'manuelmtz9k@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);

        // Crear 10 usuarios aleatorios usando Factory
        User::factory(10)->create();
    }
}