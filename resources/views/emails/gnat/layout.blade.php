<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $mailTitle ?? 'GNAT Association' }}</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f9; font-family:'Segoe UI', Arial, sans-serif;">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f4f4f9">
<tr>
<td align="center" style="padding:40px 15px;">

<table width="650" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff"
style="border-radius:24px; overflow:hidden; box-shadow:0 12px 40px rgba(49,23,66,0.12); max-width:650px;">

<tr>
<td bgcolor="#311742" align="center" style="padding:45px 30px 35px 30px; border-bottom: 5px solid #fddc6a;">

@php
    $configuredLogo = trim((string) ($logoUrl ?? config('gnat_mail.logo_url', '')));
    $logoPath = public_path(config('homepage.logo.src', 'images/logo.png'));
    if ($configuredLogo !== '') {
        $logoSrc = $configuredLogo;
    } elseif (isset($message) && is_file($logoPath)) {
        $logoSrc = $message->embed($logoPath);
    } else {
        $logoSrc = asset(config('homepage.logo.src', 'images/logo.png'));
    }
@endphp
<img src="{{ $logoSrc }}"
alt="{{ config('homepage.logo.alt', 'GNAT Association') }}"
width="140"
style="display:block; margin:0 auto 18px auto; max-width:140px; height:auto;">

<h1 style="margin:0; color:#fddc6a; font-size:28px; font-weight:bold; letter-spacing: 1px;">
GNAT Association
</h1>

<p style="margin:12px 0 0 0; color:#ffffff; font-size:13px; text-transform: uppercase; letter-spacing: 2px;">
Excellence • Community • Growth
</p>

</td>
</tr>

@if(!empty($heroHeadline))
<tr>
<td style="background-color: #ffffff; padding:36px 40px 12px 40px; text-align:center;">

<h2 style="margin:0; color:#311742; font-size:24px; font-weight:bold;">
{{ $heroHeadline }}
</h2>

<div style="height:3px; width:60px; background-color:#fddc6a; margin: 16px auto;"></div>

@if(!empty($heroSubtext))
<p style="margin-top:8px; color:#4a5568; font-size:15px; line-height:1.6;">
{!! nl2br(e($heroSubtext)) !!}
</p>
@endif

</td>
</tr>
@endif

<tr>
<td style="padding:24px 44px 28px 44px; color:#2d3748; font-size:16px; line-height:1.75;">

@yield('content')

@if(!empty($showPortalCta))
<table border="0" cellspacing="0" cellpadding="0" align="center" style="margin-top:28px; margin-bottom:12px;">
<tr>
<td align="center" bgcolor="#311742" style="border-radius:50px;">
<a href="{{ $portalUrl ?? url('/member/dashboard') }}"
style="display:inline-block; padding:14px 36px; font-size:15px; color:#fddc6a; text-decoration:none; font-weight:bold; letter-spacing:1px;">
OPEN GNAT PORTAL
</a>
</td>
</tr>
</table>
@endif

</td>
</tr>

<tr>
<td bgcolor="#311742" style="padding:32px 40px; color:#ffffff;">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top" style="font-size:13px; line-height:22px;">
<strong style="color:#fddc6a; font-size:16px;">GNAT Association</strong><br>
@php
    $contact = config('homepage.contact', []);
    $resolvedFooterAddress = $footerAddress ?? ($contact['address'] ?? null);
    $supportEmail = $contact['email'] ?? config('mail.from.address');
    $contactPhones = $contact['phones'] ?? [];
@endphp
@if(!empty($resolvedFooterAddress))
{!! nl2br(e($resolvedFooterAddress)) !!}<br><br>
@endif
@if($supportEmail)
<span style="color:#fddc6a;">E:</span> <a href="mailto:{{ $supportEmail }}" style="color:#ffffff; text-decoration:none;">{{ $supportEmail }}</a><br>
@endif
@if(!empty($contactPhones))
@foreach($contactPhones as $phone)
<span style="color:#fddc6a;">P:</span> <a href="tel:{{ $phone['tel'] ?? '' }}" style="color:#ffffff; text-decoration:none;">{{ $phone['label'] ?? $phone['tel'] ?? '' }}</a>@if(!$loop->last)<br>@endif
@endforeach
@endif
</td>
</tr>
</table>

<div style="margin-top:28px; padding-top:16px; border-top:1px solid rgba(253,220,106,0.2); text-align:center; font-size:11px; color:#a0aec0;">
© {{ date('Y') }} GNAT Association. All rights reserved.
</div>

</td>
</tr>

</table>
</td>
</tr>
</table>

</body>
</html>
