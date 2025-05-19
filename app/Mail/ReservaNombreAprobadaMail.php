<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Documento;
use App\Models\PosiblesNombres;

class ReservaNombreAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documento;

    public function __construct(Documento $documento)
    {
        $this->documento = $documento;
    }

    public function build()
    {
        return $this->subject('Tu reserva de nombre ha sido aprobada')
                    ->markdown('emails.reserva_nombre_aprobada');
    }
}
