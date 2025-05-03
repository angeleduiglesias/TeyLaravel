<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable=[
        'dni',
        'nombre',
        'apellidos',
        'telefono',
        'user_id'
    ];

    public function tramite(): HasOne
    {
        // Indicamos que un Cliente tiene un Tramite
        return $this->hasOne(Tramite::class, 'cliente_id');
    }

    public function user(): BelongsTo
    {
        // Indicamos que un Cliente pertenece a un User
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa(): BelongsTo
    {
        // Indicamos que un Cliente pertenece a una Empresa
        return $this->belongsTo(Empresa::class, 'cliente_id');
    }
}
