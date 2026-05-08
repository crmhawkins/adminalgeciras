<?php

namespace App\Mail;

use App\Models\Abono;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoAccesoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Abono $abono) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu código de acceso — Algeciras CF',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-acceso',
        );
    }
}
