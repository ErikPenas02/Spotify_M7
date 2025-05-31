<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $primaryKey = 'id_gen';
    
    protected $fillable = [
        'n_genero'
    ];
    
    // Relación con canciones
    public function canciones()
    {
        return $this->belongsToMany(Cancion::class, 'genero_cancion', 'id_genero', 'id_cancion');
    }
}
