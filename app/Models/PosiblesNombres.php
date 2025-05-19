<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosiblesNombres extends Model
{
    protected $fillable = [
        'posible_nombre1',
        'posible_nombre2',
        'posible_nombre3',
        'posible_nombre4',
        'empresa_id'
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsto(Empresa::class, 'empresa_id', 'id');
    }

}
