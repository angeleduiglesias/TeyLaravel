<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'tramite_id',
        'notario_id',
        'estado',
        'observaciones',
    ];

    public function tramite(): HasOne
    {
        return $this->hasOne(Tramite::class, 'tramite_id');
    }

    public function notario()
    {
        // Indicamos que varios documentos pueden pertenecer a un notario
        return $this->belongsTo(Notario::class, 'notario_id');
    }


}
