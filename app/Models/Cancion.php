<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancion extends Model
{
    protected $primaryKey = 'id_cancion';
    protected $table = 'canciones';
    
    protected $fillable = [
        'titulo_cancion',
        'duracion',
        'id_album'
    ];
    
    // Relación con el álbum
    public function album()
    {
        return $this->belongsTo(Album::class, 'id_album');
    }
    
    // Obtener el artista principal a través del álbum
    public function artistaPrincipal()
    {
        return $this->album->artista;
    }
    
    // Relación con artistas colaboradores
    public function artistasColaboradores()
    {
        return $this->belongsToMany(Artista::class, 'artista_cancion', 'id_cancion', 'id_artista');
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_cancion', 'id_cancion', 'id_genero');
    }
}
