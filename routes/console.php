<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {to?}', function (?string $to = null) {
    $recipient = $to ?: (string) config('mail.from.address');
    if ($recipient === '') {
        $this->error('No recipient provided. Pass an email or set MAIL_FROM_ADDRESS.');
        return Command::FAILURE;
    }

    try {
        Mail::raw(
            "SMTP test mail from Event Management.\n\nSent at: ".now()->toDateTimeString(),
            function ($message) use ($recipient) {
                $message
                    ->to($recipient)
                    ->subject('SMTP Test - Event Management');
            }
        );
    } catch (\Throwable $e) {
        $this->error('Mail send failed: '.$e->getMessage());
        return Command::FAILURE;
    }

    $this->info("Test email sent to: {$recipient}");
    return Command::SUCCESS;
})->purpose('Send a quick SMTP test email');
