<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('playlists')->insert([
            ['n_playlist' => 'Workout Mix', 'img_playlist' => 'workout.jpg', 'is_public' => true, 'is_collab' => false, 'creator' => 1],
            ['n_playlist' => 'Chill Vibes', 'img_playlist' => null, 'is_public' => false, 'is_collab' => false, 'creator' => 2],
            ['n_playlist' => 'Top Hits 2023', 'img_playlist' => 'hits2023.jpg', 'is_public' => true, 'is_collab' => true, 'creator' => 3],
            ['n_playlist' => 'Flamenco & Más', 'img_playlist' => null, 'is_public' => true, 'is_collab' => false, 'creator' => 1],
            ['n_playlist' => 'Favoritas de Sofía', 'img_playlist' => 'sofia.jpg', 'is_public' => false, 'is_collab' => true, 'creator' => 5]
        ]);
    }
}
