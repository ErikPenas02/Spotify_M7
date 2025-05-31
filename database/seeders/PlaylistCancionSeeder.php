<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PlaylistCancionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('playlist_cancion')->insert([
            ['id_playlist' => 1, 'id_cancion' => 2],
            ['id_playlist' => 1, 'id_cancion' => 3],
            ['id_playlist' => 2, 'id_cancion' => 1],
            ['id_playlist' => 2, 'id_cancion' => 6],
            ['id_playlist' => 3, 'id_cancion' => 5],
            ['id_playlist' => 3, 'id_cancion' => 4],
            ['id_playlist' => 4, 'id_cancion' => 1],
            ['id_playlist' => 4, 'id_cancion' => 6],
            ['id_playlist' => 5, 'id_cancion' => 3],
        ]);
    }
}
