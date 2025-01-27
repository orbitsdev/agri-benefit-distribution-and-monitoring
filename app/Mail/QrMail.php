<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class QrMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $beneficiary;
    public $distribution;

    /**
     * Create a new message instance.
     */
    public function __construct($beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->distribution = $beneficiary->distributionItem->distribution;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('admin@agri-project.com', 'Agri Distribution Project'),
            subject: 'QR Code for ' . $this->distribution->title . ' Distribution',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.qrmail',
            with: [
                'beneficiary' => $this->beneficiary,
                'distribution' => $this->distribution,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
