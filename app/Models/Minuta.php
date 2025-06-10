<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minuta extends Model
{
    protected $fillable = [
        'nacionalidad',
        'dni',
        'profesion',
        'estado_civil',
        'direccion',
        'nombre_conyuge',
        'dni_conyuge',
        'direccion_empresa',
        'provincia_empresa',
        'departamento_empresa',
        'objetivo',
        'monto_capital',
        'apoderado',
        'dni_apoderado',
        'ciudad',
        'fecha_registro',
        'tipo_formulario',
        'documento_id'
    ];
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id', 'id');
    }
    public function socios():HasMany
    {
        return $this->hasMany(Socio::class, 'socio_id', 'id');
    }

    public function aportes():HasMany
    {
        return $this->hasMany(Aporte::class, 'aporte_id', 'id');
    }
}
