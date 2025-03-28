<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notario extends Model
{
    protected $table = 'notarios';

    protected $fillable= [
        'full_name',
        'telefono',
        'direccion',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        // Indicamos que este Cliente pertenece a un User
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documento(): HasMany
    {
        // Indicamos que este Notario tiene muchos Documentos
        return $this->hasMany(Documento::class);
    }
}
