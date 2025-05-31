<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ArtistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('artistas')->insert([
            ['n_artista' => 'Rosalía', 'desc_artista' => 'Flamenco urbano', 'img_artista' => 'rosalia.jpg'],
            ['n_artista' => 'Bad Bunny', 'desc_artista' => 'Trap latino', 'img_artista' => 'badbunny.jpg'],
            ['n_artista' => 'Dua Lipa', 'desc_artista' => 'Pop internacional', 'img_artista' => 'dualipa.jpg'],
            ['n_artista' => 'Coldplay', 'desc_artista' => 'Rock alternativo', 'img_artista' => 'coldplay.jpg'],
            ['n_artista' => 'Bizarrap', 'desc_artista' => 'Productor argentino', 'img_artista' => 'biza.jpg'],
            ['n_artista' => 'The Weeknd', 'desc_artista' => 'R&B contemporáneo', 'img_artista' => 'weeknd.jpg']
        ]);
    }
}
