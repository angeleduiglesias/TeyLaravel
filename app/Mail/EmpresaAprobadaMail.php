<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmpresaAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $empresa;

    /**
     * Create a new message instance.
     */
    public function __construct($cliente, $empresa)
    {
        $this->cliente = $cliente;
        $this->empresa = $empresa;
    }

    public function build()
    {
        return $this->subject('¡Nombre de empresa aprobado!')
            ->view('emails.empresa_aprobada');
    }

    
}
