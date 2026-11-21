<?php

namespace App\Mail;

use App\Models\Brief;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBriefReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Brief $brief)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo brief recibido: '.(data_get($this->brief->data, 'brand_name') ?: data_get($this->brief->data, 'legal_name') ?: 'Sin nombre'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.briefs.received');
    }
}
