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

Artisan::command('gnat:mail-test-admin {template? : Admin template key e.g. a01_profile_submitted}', function (?string $template = null) {
    $mail = app(\App\Services\GnatMailService::class);
    $recipients = $mail->adminRecipients();

    if ($recipients === []) {
        $this->error('No admin recipients configured. Set GNAT_ADMIN_MAIL or MAIL_FROM_ADDRESS in .env');
        return Command::FAILURE;
    }

    $this->info('Admin recipients: '.implode(', ', $recipients));

    $samples = [
        'a01_profile_submitted' => [
            'memberName' => 'Test Member',
            'email' => 'member@example.com',
            'mobile' => '+91 9876543210',
            'submittedOn' => now()->format('d M Y, h:i A'),
        ],
        'a02_subscription_payment' => [
            'memberName' => 'Test Member',
            'membershipPlan' => 'Annual • online',
            'transactionId' => 'TXN-TEST-001',
            'amount' => 'INR 1,000.00',
            'paymentDate' => now()->format('d M Y, h:i A'),
        ],
        'a14_support_request' => [
            'memberName' => 'Test Visitor',
            'ticketId' => 'TESTTICKET',
            'supportSubject' => 'General',
            'submittedOn' => now()->format('d M Y, h:i A'),
            'supportBody' => 'This is a test support message from gnat:mail-test-admin.',
        ],
    ];

    if ($template === null) {
        $template = 'a01_profile_submitted';
    }

    if (! isset($samples[$template])) {
        $this->error("Unknown template: {$template}");
        $this->line('Available samples: '.implode(', ', array_keys($samples)));
        return Command::FAILURE;
    }

    try {
        $mail->sendAdmin($template, $samples[$template]);
    } catch (\Throwable $e) {
        $this->error('Admin mail failed: '.$e->getMessage());
        return Command::FAILURE;
    }

    $this->info("Admin test email ({$template}) dispatched to: ".implode(', ', $recipients));
    $this->line('Check storage/logs/laravel.log for "GNAT mail sent" confirmation.');

    return Command::SUCCESS;
})->purpose('Send a GNAT admin notification test email');
