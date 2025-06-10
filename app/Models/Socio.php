<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $fillable = [
        'nombre',
        'nacionalidad',
        'dni',
        'profesion',
        'estado_civil',
        'nombre_conyuge',
        'dni_conyuge',
        'minuta_id'
    ];

    public function minuta()
    {
        return $this->belongsTo(Minuta::class, 'minuta_id', 'id');
    }
}
