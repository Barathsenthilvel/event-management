{{--
    Letter-style avatar (no photos). Pass $user (optional) and/or $letter, plus $class for sizing.
--}}
@php
    $class = $class ?? 'h-10 w-10 text-sm';
    $initials = $letter ?? null;
    if ($initials === null && isset($user) && $user) {
        $fn = trim((string) ($user->first_name ?? ''));
        $ln = trim((string) ($user->last_name ?? ''));
        if ($fn !== '' || $ln !== '') {
            $initials = strtoupper(\Illuminate\Support\Str::substr($fn, 0, 1).\Illuminate\Support\Str::substr($ln, 0, 1));
        } else {
            $n = trim((string) ($user->name ?? ''));
            $initials = $n !== '' ? strtoupper(\Illuminate\Support\Str::substr($n, 0, 2)) : '?';
        }
    }
    $initials = $initials ?: '?';
    $seed = (string) ((isset($user) && $user) ? (($user->id ?? '') ?: ($user->email ?? '')) : $initials);
    $hash = abs(crc32($seed));
    $palettes = [
        'bg-[#0d9488] text-white',
        'bg-[#351c42] text-[#fddc6a]',
        'bg-[#7c3aed] text-white',
        'bg-[#c2410c] text-white',
        'bg-[#0369a1] text-white',
        'bg-[#965995] text-white',
    ];
    $palette = $palettes[$hash % count($palettes)];
@endphp
<span class="inline-flex shrink-0 items-center justify-center rounded-full font-extrabold leading-none {{ $palette }} {{ $class }}">{{ $initials }}</span>
