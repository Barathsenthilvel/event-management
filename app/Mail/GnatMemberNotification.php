<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GnatMemberNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  Template variables (not named $viewData — reserved on {@see Mailable})
     */
    public function __construct(
        public string $templateKey,
        public string $mailSubject,
        public array $payload = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        $defaults = [
            'templateKey' => $this->templateKey,
            'mailTitle' => $this->mailSubject,
            'logoUrl' => config('gnat_mail.logo_url'),
            'heroHeadline' => null,
            'heroSubtext' => null,
            'showPortalCta' => false,
        ];

        return new Content(
            html: 'emails.gnat.member',
            with: array_merge($defaults, $this->payload),
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
