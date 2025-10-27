<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuDonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $don;
    public $projetNom;

    public function __construct($don, string $projetNom)
    {
        $this->don = $don;
        $this->projetNom = $projetNom;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reçu de votre don - ADIS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recu-don',
            with: [
                'don' => $this->don,
                'projetNom' => $this->projetNom,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}