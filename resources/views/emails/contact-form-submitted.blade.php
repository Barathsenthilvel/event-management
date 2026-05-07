<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact form</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; color: #334155; max-width: 36rem; margin: 0 auto; padding: 1.5rem;">
    <h1 style="font-size: 1.125rem; color: #351c42; margin: 0 0 1rem;">New message — website contact form</h1>
    <table style="width: 100%; border-collapse: collapse; font-size: 0.9375rem;">
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top; width: 6rem;">Name</td>
            <td style="padding: 0.35rem 0;">{{ $payload['name'] }}</td>
        </tr>
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Email</td>
            <td style="padding: 0.35rem 0;"><a href="mailto:{{ $payload['email'] }}">{{ $payload['email'] }}</a></td>
        </tr>
        @if(! empty($payload['phone']))
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Phone</td>
            <td style="padding: 0.35rem 0;">{{ $payload['phone'] }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 0.35rem 0; font-weight: 600; vertical-align: top;">Subject</td>
            <td style="padding: 0.35rem 0;">{{ $payload['subject'] }}</td>
        </tr>
    </table>
    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
        <p style="margin: 0 0 0.5rem; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Message</p>
        <div style="white-space: pre-wrap;">{{ $payload['message'] }}</div>
    </div>
    @if(! empty($meta['ip']))
    <p style="margin-top: 1.25rem; padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem; font-size: 0.8125rem; color: #64748b;">
        IP: {{ $meta['ip'] }}
        @if(! empty($meta['user_agent']))
            <br><span style="word-break: break-all;">{{ \Illuminate\Support\Str::limit($meta['user_agent'], 240) }}</span>
        @endif
    </p>
    @endif
    <p style="margin-top: 1.5rem; font-size: 0.8125rem; color: #94a3b8;">{{ config('app.name') }}</p>
</body>
</html>
