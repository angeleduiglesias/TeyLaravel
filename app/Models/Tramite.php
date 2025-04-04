<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tramite extends Model
{
    protected $fillable=[
        'estado',
        'cliente_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function Cliente(): HasOne
    {
        //Indicamos que un tramite pertenece a un cliente
        return $this->hasOne(Cliente::class, 'cliente_id');
    }

    public function Documento(): HasMany
    {
        // Indicamos que un tramite tiene muchos documentos
        return $this->hasMany(Documento::class, 'tramite_id');
    }

    public function Pago(): HasMany
    {
        // Indicamos que un tramite tiene muchos pagos
        return $this->hasMany(Pago::class, 'tramite_id');
    }
}