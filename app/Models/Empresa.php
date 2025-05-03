<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    protected $fillable = [
        'tipo_aporte',
        'rango_capital',
        'rubro', 
        'actividades',
        'tipo_empresa',
        'nombre_empresa',
        'posible_nombre1',
        'posible_nombre2',
        'posible_nombre3',
        'posible_nombre4',
        'numero_socios',
        'cliente_id',
    ];

    public function cliente(): HasOne
    {
        // Indicamos que una Empresa pertenece a un Cliente
        return $this->hasOne(Cliente::class, 'cliente_id');
    }
}
