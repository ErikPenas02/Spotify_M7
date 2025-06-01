<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rol extends Model
{
    protected $primaryKey = 'id_rol';
    
    protected $fillable = [
        'nombre_rol'
    ];
    protected $table = 'roles';
    
    // Relación con usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
