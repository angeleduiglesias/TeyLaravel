<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'numero_socios',
        'cliente_id',
    ];

    public function cliente(): BelongsTo
    {
        // Indicamos que una Empresa pertenece a un Cliente
        return $this->belongsto(Cliente::class, 'cliente_id', 'id');
    }

    public function posiblesNombres(): HasOne
    {
        return $this->hasOne(PosiblesNombres::class, 'empresa_id', 'id');
    }
}
