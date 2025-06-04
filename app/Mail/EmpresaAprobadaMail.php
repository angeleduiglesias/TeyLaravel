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
    public function __construct()
    {
        $this->cliente = $cliente;
        $this->empresa = $empresa;
    }

    public function build()
    {
        return $this->subject('¡Nombre de empresa aprobado!')
            ->view('emails.empresa_aprobada');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Empresa Aprobada Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
