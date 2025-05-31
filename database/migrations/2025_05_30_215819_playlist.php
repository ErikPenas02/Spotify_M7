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
        Schema::create('playlists', function (Blueprint $table) {
            $table->id('id_playlist');
            $table->string('n_playlist');
            $table->string('img_playlist')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_collab')->default(false);
            $table->foreignId('creator')->constrained('users', 'id_user');
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
