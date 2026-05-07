<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, phone: ?string, subject: string, message: string}  $payload
     */
    public function __construct(
        public array $payload,
    ) {}

    public function envelope(): Envelope
    {
        $replyEmail = $this->payload['email'];

        return new Envelope(
            subject: '[Website contact] '.$this->payload['subject'],
            replyTo: [
                new Address($replyEmail, $this->payload['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.contact-form-submitted',
            with: [
                'payload' => $this->payload,
                'meta' => [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
