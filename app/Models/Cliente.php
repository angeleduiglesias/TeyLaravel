<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $fillable=[
        'dni',
        'full_name',
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
}
