<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aporte extends Model
{
    protected $fillable = [
        'descripcion',
        'monto',
        'minuta_id'
    ];

    public function minuta()
    {
        return $this->belongsTo(Minuta::class, 'minuta_id', 'id');
    }
}
