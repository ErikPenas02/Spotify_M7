<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('albums')->insert([
            ['titulo_album' => 'Motomami', 'id_artista' => 1, 'portada_album' => 'motomami.jpg', 'desc_album' => 'Álbum de Rosalía', 'fecha_estreno' => '2022-03-18'],
            ['titulo_album' => 'Un Verano Sin Ti', 'id_artista' => 2, 'portada_album' => 'verano.jpg', 'desc_album' => 'Bad Bunny', 'fecha_estreno' => '2022-05-06'],
            ['titulo_album' => 'Future Nostalgia', 'id_artista' => 3, 'portada_album' => 'nostalgia.jpg', 'desc_album' => null, 'fecha_estreno' => '2020-03-27'],
            ['titulo_album' => 'Music of the Spheres', 'id_artista' => 4, 'portada_album' => 'coldplay.jpg', 'desc_album' => 'Coldplay álbum', 'fecha_estreno' => '2021-10-15'],
            ['titulo_album' => 'Starboy', 'id_artista' => 6, 'portada_album' => 'starboy.jpg', 'desc_album' => null, 'fecha_estreno' => '2016-11-25']
        ]);
    }
}
