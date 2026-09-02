@props(['user' => null, 'name' => null, 'size' => 28])

{{--
    A photograph if the author has uploaded one, initials on a coloured disc if
    not.

    Deliberately not Gravatar in either case: that would tell a third party the
    email address of every reader who leaves a comment, on every page view,
    which is a poor trade for a picture. The initials hue comes from the name,
    so the same person is the same colour everywhere without anything being
    stored about them.
--}}
@php
    $label = $name ?? $user?->name ?? '';
    $photo = $user?->avatarUrl($size * 2);

    $initials = collect(preg_split('/[\s\-]+/', trim($label)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: '?';

    $hue = crc32($label) % 360;
    $box = 'inline-flex shrink-0 select-none items-center justify-center overflow-hidden rounded-full';
@endphp

@if($photo)
    <img src="{{ $photo }}" @if($set = $user?->avatarSrcset()) srcset="{{ $set }}" @endif
         sizes="{{ $size }}px"
         alt="" aria-hidden="true" loading="lazy" decoding="async"
         width="{{ $size }}" height="{{ $size }}"
         {{ $attributes->merge(['class' => $box.' bg-paper-3 object-cover']) }}
         style="width: {{ $size }}px; height: {{ $size }}px;">
@else
    <span aria-hidden="true"
          {{ $attributes->merge(['class' => $box.' font-mono font-medium leading-none text-white']) }}
          style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.38) }}px; background: hsl({{ $hue }} 42% 38%);">{{ $initials }}</span>
@endif
