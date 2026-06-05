<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HintEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Item $item,
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['recipient_name'] . ' has a diamond in mind — a little hint ♥',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hint',
        );
    }
}
