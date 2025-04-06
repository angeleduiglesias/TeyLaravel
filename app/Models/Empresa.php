<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    protected $fillable = [
        'nombre_empresa',
        'actividad_comercial',
        'tipo_empresa'
    ];

    public function cliente(): HasOne
    {
        // Indicamos que una Empresa pertenece a un Cliente
        return $this->hasOne(Cliente::class, 'cliente_id');
    }
}
