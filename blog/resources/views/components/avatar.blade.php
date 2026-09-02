@props(['name' => '', 'size' => 28])

{{--
    Initials on a coloured disc.

    Deliberately not Gravatar: that would tell a third party the email address
    of every reader who leaves a comment, on every page view, which is a poor
    trade for a picture. The hue comes from the name, so the same person is the
    same colour everywhere without anything being stored.
--}}
@php
    $initials = collect(preg_split('/[\s\-]+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: '?';

    $hue = crc32($name) % 360;
@endphp

<span aria-hidden="true"
      {{ $attributes->merge(['class' => 'inline-flex shrink-0 select-none items-center justify-center rounded-full font-mono font-medium leading-none text-white']) }}
      style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.38) }}px; background: hsl({{ $hue }} 42% 38%);">{{ $initials }}</span>
