<?php

namespace App\Observers;

use App\Models\Documento;
use App\Mail\ReservaNombreAprobadaMail;
use Illuminate\Support\Facades\Mail;
use App\Models\PosiblesNombres;
use App\Models\Cliente;
use App\Models\Tramite;
use App\Models\Pago;

class DocumentoObserver
{
    public function updating(Documento $documento)
    {
        if (
            $documento->tipo_documento === 'reserva_nombre' &&
            $documento->getOriginal('estado') !== 'aprobado' &&
            $documento->estado === 'aprobado'
        ) {
            $cliente = $documento->tramite?->cliente;
            $email = $cliente?->user?->email;

            // if ($email) {
            //     Mail::to($email)->send(new ReservaNombreAprobadaMail($documento));
            // }
        }
    }
}
