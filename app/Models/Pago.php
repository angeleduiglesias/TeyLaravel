<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'tramite_id',
        'monto',
        'fecha',
        'comprobante',
        'estado',
        'tipo_pago',
    ];

    public function tramite(): BelongsTo
    {
        //Indicamos que varios pagos pertenecen a un tramite
        return $this->belongsTo(Tramite::class, 'tramite_id', 'id');
    }
}
