<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artista extends Model
{
    protected $primaryKey = 'id_artista';
    
    protected $fillable = [
        'n_artista',
        'desc_artista',
        'img_artista'
    ];
    
    // Álbumes del artista
    public function albums()
    {
        return $this->hasMany(Album::class, 'id_artista');
    }
    
    // Canciones donde es colaborador
    public function cancionesColaboraciones()
    {
        return $this->belongsToMany(Cancion::class, 'artista_cancion', 'id_artista', 'id_cancion');
    }
}
