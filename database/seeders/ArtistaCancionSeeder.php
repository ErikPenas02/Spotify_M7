<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ArtistaCancionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('artista_cancion')->insert([
            ['id_artista' => 1, 'id_cancion' => 1],
            ['id_artista' => 2, 'id_cancion' => 2],
            ['id_artista' => 3, 'id_cancion' => 3],
            ['id_artista' => 4, 'id_cancion' => 4],
            ['id_artista' => 6, 'id_cancion' => 5],
            ['id_artista' => 1, 'id_cancion' => 6],
            ['id_artista' => 6, 'id_cancion' => 6], // Rosalía + The Weeknd
        ]);
    }
}
