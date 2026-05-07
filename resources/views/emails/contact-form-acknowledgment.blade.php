<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting us</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; color: #334155; max-width: 36rem; margin: 0 auto; padding: 1.5rem;">
    <p style="margin: 0 0 1rem; font-size: 0.9375rem;">Dear {{ $payload['name'] }},</p>
    <p style="margin: 0 0 1rem; font-size: 0.9375rem;">
        Thank you for contacting <strong style="color: #351c42;">GNAT Association</strong>. This email confirms we have received your message from our website contact form.
    </p>
    <div style="margin: 1.25rem 0; padding: 1rem; background: #f8f6fa; border-radius: 0.75rem; border: 1px solid #e8e4ed;">
        <p style="margin: 0 0 0.5rem; font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #965995;">Your submission</p>
        <p style="margin: 0; font-size: 0.9375rem;"><strong>Subject:</strong> {{ $payload['subject'] }}</p>
        @if(! empty($payload['phone']))
            <p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: #64748b;"><strong>Phone:</strong> {{ $payload['phone'] }}</p>
        @endif
        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 0.8125rem; font-weight: 600; color: #64748b;">Message</p>
            <div style="margin-top: 0.35rem; font-size: 0.875rem; white-space: pre-wrap;">{{ $payload['message'] }}</div>
        </div>
    </div>
    <p style="margin: 0 0 1rem; font-size: 0.9375rem;">
        Our team will review your enquiry and get back to you as soon as possible. If your matter is urgent, you can reach us directly at
        <a href="mailto:{{ $adminEmail }}" style="color: #965995; font-weight: 600;">{{ $adminEmail }}</a>.
    </p>
    <p style="margin: 0 0 1rem; font-size: 0.9375rem;">
        With gratitude,<br>
        <strong style="color: #351c42;">GNAT Association</strong>
    </p>
    <p style="margin: 1.5rem 0 0; padding-top: 1rem; border-top: 1px solid #e2e8f0; font-size: 0.75rem; color: #94a3b8;">
        This is an automated confirmation. Please do not reply to this message unless your mail client opens a conversation with our office inbox.
    </p>
</body>
</html>
