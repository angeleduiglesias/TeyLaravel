<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tramite extends Model
{
    protected $fillable=[
        'estado',
        'cliente_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function documentos(): HasMany
    {
        // Indicamos que un tramite tiene muchos documentos
        return $this->hasMany(Documento::class, 'tramite_id', 'id');
    }

    public function pagos(): HasMany
    {
        // Indicamos que un tramite tiene muchos pagos
        return $this->hasMany(Pago::class, 'tramite_id', 'id');
    }
}