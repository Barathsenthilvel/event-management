<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormAcknowledgment extends Mailable
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
        $adminInbox = (string) (config('homepage.contact_form_to') ?: config('homepage.contact.email'));

        return new Envelope(
            subject: 'We received your message — GNAT Association',
            replyTo: [
                new Address($adminInbox, 'GNAT Association'),
            ],
        );
    }

    public function content(): Content
    {
        $adminInbox = (string) (config('homepage.contact_form_to') ?: config('homepage.contact.email'));

        return new Content(
            html: 'emails.contact-form-acknowledgment',
            with: [
                'payload' => $this->payload,
                'adminEmail' => $adminInbox,
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
