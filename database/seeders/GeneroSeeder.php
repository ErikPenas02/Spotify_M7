<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('generos')->insert([
            ['n_genero' => 'Pop'],
            ['n_genero' => 'Rock'],
            ['n_genero' => 'Flamenco'],
            ['n_genero' => 'Trap'],
            ['n_genero' => 'Urbano'],
            ['n_genero' => 'Electrónica'],
            ['n_genero' => 'R&B'],
        ]);
    }
}
