<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CancionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('canciones')->insert([
            ['titulo_cancion' => 'Saoko', 'duracion' => '00:02:17', 'id_album' => 1],
            ['titulo_cancion' => 'Tití Me Preguntó', 'duracion' => '00:04:04', 'id_album' => 2],
            ['titulo_cancion' => 'Levitating', 'duracion' => '00:03:23', 'id_album' => 3],
            ['titulo_cancion' => 'My Universe', 'duracion' => '00:03:46', 'id_album' => 4],
            ['titulo_cancion' => 'Blinding Lights', 'duracion' => '00:03:20', 'id_album' => 5],
            ['titulo_cancion' => 'La Fama', 'duracion' => '00:03:07', 'id_album' => 1],
        ]);
    }
}
