<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GeneroCancionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genero_cancion')->insert([
            ['id_genero' => 3, 'id_cancion' => 1], // Flamenco
            ['id_genero' => 4, 'id_cancion' => 2], // Trap
            ['id_genero' => 1, 'id_cancion' => 3], // Pop
            ['id_genero' => 2, 'id_cancion' => 4], // Rock
            ['id_genero' => 7, 'id_cancion' => 5], // R&B
            ['id_genero' => 3, 'id_cancion' => 6],
            ['id_genero' => 7, 'id_cancion' => 6],
        ]);
    }
}
