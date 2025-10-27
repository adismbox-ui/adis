<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevisNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $donateur;
    public $don;
    public $projet;

    /**
     * Create a new message instance.
     */
    public function __construct($donateur, $don, $projet = null)
    {
        $this->donateur = $donateur;
        $this->don = $don;
        $this->projet = $projet;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📋 Devis personnalisé - ADIS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.devis-notification',
            with: [
                'donateur' => $this->donateur,
                'don' => $this->don,
                'projet' => $this->projet,
            ],
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