<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artista_cancion', function (Blueprint $table) {
            $table->id('id_artista_cancion');
            $table->foreignId('id_artista')->constrained('artistas', 'id_artista');
            $table->foreignId('id_cancion')->constrained('canciones', 'id_cancion');
            $table->timestamps();
            $table->unique(['id_artista', 'id_cancion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
