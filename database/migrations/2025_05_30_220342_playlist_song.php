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
        Schema::create('playlist_cancion', function (Blueprint $table) {
            $table->id('id_playlist_cancion');
            $table->foreignId('id_playlist')->constrained('playlists', 'id_playlist');
            $table->foreignId('id_cancion')->constrained('canciones', 'id_cancion');
            $table->timestamps();
            $table->unique(['id_playlist', 'id_cancion']);
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
