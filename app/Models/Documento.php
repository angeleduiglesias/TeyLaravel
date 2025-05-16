<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'tramite_id',
        'notario_id',
        'estado',
        'observaciones',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function notario()
    {
        // Indicamos que varios documentos pueden pertenecer a un notario
        return $this->belongsTo(Notario::class, 'notario_id');
    }


}
