<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rol extends Model
{
    protected $primaryKey = 'id_rol';
    
    protected $fillable = [
        'nombre_rol'
    ];
    
    // Relación con usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
