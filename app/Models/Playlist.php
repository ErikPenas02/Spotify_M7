<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Playlist extends Model
{
    protected $primaryKey = 'id_playlist';
    
    protected $fillable = [
        'n_playlist',
        'img_playlist',
        'is_public',
        'is_collab',
        'creator'
    ];
    
    // Relación con el usuario
    public function usuario() : BelongsTo
    {
        return $this->belongsTo(User::class, 'creator', 'id_user');
    }
    
    // Relación con canciones
    public function canciones()
    {
        return $this->belongsToMany(Cancion::class, 'playlist_cancion', 'id_playlist', 'id_cancion');
    }
}
