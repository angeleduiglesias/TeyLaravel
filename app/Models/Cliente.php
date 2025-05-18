<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        return $this->hasOne(Tramite::class, 'cliente_id', 'id');
    }

    public function user(): BelongsTo
    {
        // Indicamos que un Cliente pertenece a un User
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa(): HasOne
    {
        // Indicamos que un Cliente pertenece a una Empresa
        return $this->hasOne(Empresa::class, 'cliente_id', 'id');
    }
}
