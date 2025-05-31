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
        Schema::create('albums', function (Blueprint $table) {
            $table->id('id_album');
            $table->string('titulo_album');
            $table->foreignId('id_artista')->constrained('artistas', 'id_artista');
            $table->string('portada_album');
            $table->string('desc_album')->nullable();
            $table->date('fecha_estreno')->nullable();
            $table->timestamps();
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
