<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $primaryKey = 'id_album';
    
    protected $fillable = [
        'titulo_album',
        'id_artista',
        'portada_album',
        'desc_album',
        'fecha_estreno'
    ];
    
    // Relación con el artista principal
    public function artista()
    {
        return $this->belongsTo(Artista::class, 'id_artista');
    }
    
    // Relación con las canciones
    public function canciones()
    {
        return $this->hasMany(Cancion::class, 'id_album');
    }
}
