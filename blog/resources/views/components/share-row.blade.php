{{--
    Share row.

    Worth being clear about how this works, because it is easy to assume
    otherwise: a share intent carries a URL and some text, never a picture. The
    image and the summary in the resulting card are read by the network from the
    og: tags on the page being shared, which is why the 1200x630 crop and the
    meta tags in the head are the part that actually decides what the post looks
    like on Facebook, X, LinkedIn, Slack and iMessage.

    Copy and print need script; both degrade to being absent rather than broken,
    and the native share button is only revealed where the browser has the API.
--}}
@props(['post'])

@php
    $url = $post->canonical_url ?: route('blog.show', $post);
    $text = $post->social_message ?: $post->title;
    $summary = \Illuminate\Support\Str::limit($post->excerpt ?: $post->title, 160);
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    <span class="mr-1 font-mono text-[0.68rem] uppercase tracking-label text-ink-soft">Share</span>

    @foreach ([
        ['X', 'https://twitter.com/intent/tweet?text='.rawurlencode($text).'&url='.rawurlencode($url),
            '<path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L2.4 2h6.4l4.4 5.9L18.9 2Zm-1.1 18h1.7L8.3 3.8H6.5L17.8 20Z"/>'],
        ['Facebook', 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url),
            '<path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.3 0-1.7.8-1.7 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12Z"/>'],
        ['LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url='.rawurlencode($url),
            '<path d="M20.5 2h-17A1.5 1.5 0 0 0 2 3.5v17A1.5 1.5 0 0 0 3.5 22h17a1.5 1.5 0 0 0 1.5-1.5v-17A1.5 1.5 0 0 0 20.5 2ZM8 19H5V9.5h3V19ZM6.5 8.2a1.7 1.7 0 1 1 0-3.5 1.7 1.7 0 0 1 0 3.5ZM19 19h-3v-5c0-1.2-.4-2-1.5-2A1.7 1.7 0 0 0 13 13.8V19h-3V9.5h2.9v1.3A3.2 3.2 0 0 1 15.8 9c2 0 3.2 1.3 3.2 4V19Z"/>'],
        ['Reddit', 'https://www.reddit.com/submit?url='.rawurlencode($url).'&title='.rawurlencode($post->title),
            '<path d="M22 12a2.1 2.1 0 0 0-3.5-1.5 10.3 10.3 0 0 0-5.4-1.7l.9-4.3 3 .6a1.5 1.5 0 1 0 .2-1l-3.6-.8a.5.5 0 0 0-.6.4l-1 4.8a10.3 10.3 0 0 0-5.5 1.7A2.1 2.1 0 1 0 4 13.8a4 4 0 0 0 0 .5c0 2.9 3.6 5.3 8 5.3s8-2.4 8-5.3a4 4 0 0 0 0-.5A2.1 2.1 0 0 0 22 12ZM8 13.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm7.9 4a5.6 5.6 0 0 1-3.9 1.2 5.6 5.6 0 0 1-3.9-1.2.4.4 0 1 1 .6-.6 4.9 4.9 0 0 0 3.3 1 4.9 4.9 0 0 0 3.3-1 .4.4 0 1 1 .6.6Zm-.4-2.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/>'],
        ['Email', 'mailto:?subject='.rawurlencode($post->title).'&body='.rawurlencode($summary."\n\n".$url),
            '<path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4.2-8 5-8-5V6l8 5 8-5v2.2Z"/>'],
    ] as [$label, $href, $icon])
        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" title="Share on {{ $label }}"
           class="inline-flex h-9 w-9 items-center justify-center border border-paper-4 text-ink-soft transition-colors hover:border-ink-ink hover:bg-ink-ink hover:text-paper">
            <span class="sr-only">Share on {{ $label }}</span>
            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true">{!! $icon !!}</svg>
        </a>
    @endforeach

    <button type="button" data-copy-link="{{ $url }}" title="Copy link"
            class="inline-flex h-9 items-center gap-2 border border-paper-4 px-3 font-mono text-[0.68rem] uppercase tracking-label text-ink-soft transition-colors hover:border-ink-ink hover:bg-ink-ink hover:text-paper">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M10 13a5 5 0 0 0 7.1 0l3-3a5 5 0 0 0-7.1-7.1L11.5 4.5"/>
            <path d="M14 11a5 5 0 0 0-7.1 0l-3 3a5 5 0 0 0 7.1 7.1l1.4-1.4"/>
        </svg>
        <span data-copy-label>Copy link</span>
    </button>

    {{-- Revealed by script only where navigator.share exists, which in practice
         means a phone. Hidden by default so it never appears as a dead control. --}}
    <button type="button" data-native-share data-share-title="{{ $post->title }}" data-share-text="{{ $summary }}" data-share-url="{{ $url }}"
            hidden title="Share"
            class="inline-flex h-9 items-center gap-2 border border-paper-4 px-3 font-mono text-[0.68rem] uppercase tracking-label text-ink-soft transition-colors hover:border-ink-ink hover:bg-ink-ink hover:text-paper">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>
        </svg>
        More
    </button>

    <button type="button" data-print title="Print"
            class="hidden h-9 w-9 items-center justify-center border border-paper-4 text-ink-soft transition-colors hover:border-ink-ink hover:bg-ink-ink hover:text-paper sm:inline-flex">
        <span class="sr-only">Print this post</span>
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M6 9V3h12v6M6 18H4v-6h16v6h-2"/><path d="M8 14h8v7H8z"/>
        </svg>
    </button>
</div>
