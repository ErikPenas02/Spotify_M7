<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'name' => 'Alice',
                'surname' => 'Smith',
                'email' => 'admin@example.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 1,
                'is_active' => true,
            ],
            [
                'username' => 'juan23',
                'name' => 'Juan',
                'surname' => 'Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2,
                'is_active' => true,
            ],
            [
                'username' => 'lola98',
                'name' => 'Lola',
                'surname' => 'Martínez',
                'email' => 'lola@example.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2,
                'is_active' => true,
            ],
            [
                'username' => 'carlos_dev',
                'name' => 'Carlos',
                'surname' => 'Torres',
                'email' => 'carlos@example.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2,
                'is_active' => true,
            ],
            [
                'username' => 'sofia123',
                'name' => 'Sofía',
                'surname' => 'López',
                'email' => 'sofia@example.com',
                'password' => Hash::make('qweQWE123'),
                'id_rol' => 2,
                'is_active' => false,
            ]
        ]);
    }
}
