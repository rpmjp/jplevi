@extends('layouts.blog')
@section('title', $post->meta_title ?: $post->title)
@section('description', $post->meta_description ?: $post->excerpt)
@section('canonical', $post->canonical_url ?: route('blog.show', $post))

@php
    $shareUrl = $post->canonical_url ?: route('blog.show', $post);
    $shareImage = \App\Models\Rendition::social($post->cover_path);
    $shareText = $post->meta_description ?: $post->excerpt ?: $post->title;
    $hero = \App\Models\Rendition::url($post->cover_path, 1200);
    $heroSet = \App\Models\Rendition::srcset($post->cover_path);
    $heroSize = \App\Models\Rendition::dimensions($post->cover_path);
@endphp

@push('head')
{{--
    What a link preview is built from.

    The networks read these tags off the page; the share buttons only ever hand
    them a URL. So this block, and the 1200x630 crop it points at, is what
    decides whether the post arrives in a feed as a card with a picture or as a
    bare blue link.
--}}
<meta property="og:type" content="article">
<meta property="og:site_name" content="JP LEVI INC.">
<meta property="og:url" content="{{ $shareUrl }}">
<meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
<meta property="og:description" content="{{ $shareText }}">
<meta property="og:locale" content="en_US">
@if($shareImage)
    {{-- Both spellings of the URL. Some scrapers read only the secure one, and
         one that finds no image it can use posts the link with no picture at
         all rather than falling back to another tag. --}}
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:secure_url" content="{{ $shareImage }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $post->cover_alt ?: $post->title }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <meta name="twitter:image:alt" content="{{ $post->cover_alt ?: $post->title }}">
@else
    {{-- No picture, so ask for the compact card rather than letting the network
         pick something arbitrary off the page. --}}
    <meta name="twitter:card" content="summary">
@endif
<meta name="twitter:title" content="{{ $post->meta_title ?: $post->title }}">
<meta name="twitter:description" content="{{ $shareText }}">
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at?->toIso8601String() }}">
<meta property="article:author" content="{{ $post->author->name }}">
@if($primaryCategory = $post->categories->first())
    <meta property="article:section" content="{{ $primaryCategory->name }}">
@endif
@foreach($post->categories as $category)
    <meta property="article:tag" content="{{ $category->name }}">
@endforeach
{{-- The plain document-level author, which search engines read and og: does
     not replace. --}}
<meta name="author" content="{{ $post->author->name }}">
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article class="mx-auto max-w-5xl px-6 pt-14 sm:px-10 sm:pt-20">
    @if($preview)
        <p class="mb-8 border-l-2 border-ember pl-4 font-mono text-[0.78rem] text-ink-body">
            Preview of an unpublished draft. Anyone with this link can read it.
        </p>
    @endif

    @if($post->categories->isNotEmpty())
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
            @foreach($post->categories as $category)
                <a href="{{ route('blog.topic', $category) }}"
                   class="font-mono text-[0.68rem] uppercase tracking-label text-brand transition-colors hover:text-ink-ink">{{ $category->name }}</a>
            @endforeach
        </div>
    @endif

    <h1 class="biz-display mt-4 max-w-[20ch] text-[clamp(2.2rem,6.5vw,4.6rem)]">{{ $post->title }}</h1>

    @if($post->excerpt)
        <p class="mt-8 max-w-prose border-l-2 border-brand pl-5 font-sans text-[1.1rem] leading-[1.55] text-ink-ink">
            {{ $post->excerpt }}
        </p>
    @endif

    {{-- Byline and share on one rule: who wrote it and how to pass it on are
         the two things a reader wants at this point, and neither deserves its
         own band of whitespace. --}}
    <div class="mt-9 flex flex-wrap items-center justify-between gap-x-8 gap-y-5 border-y border-paper-3 py-5">
        <div class="flex items-center gap-3">
            <x-avatar :user="$post->author" :size="40" />
            <div>
                <a href="{{ route('blog.author', $post->author) }}"
                   class="block font-sans text-[0.95rem] font-medium text-ink-ink transition-colors hover:text-brand">{{ $post->author->name }}</a>
                <p class="mt-0.5 font-mono text-[0.72rem] text-ink-soft">
                    <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('F j, Y') ?? 'Unpublished' }}</time>
                    &middot; {{ $post->reading_minutes }} min read
                </p>
            </div>
        </div>

        <x-share-row :post="$post" />
    </div>

    {{-- Featured image.

         The real dimensions go on the tag so the page reserves the right box
         and nothing jumps when it arrives, and it loads eagerly at high
         priority because it is the one image certain to be on screen. Capped in
         height so a tall photograph does not take the whole first screen and
         push the writing out of sight. --}}
    @if($hero)
        <figure class="mt-10">
            <img src="{{ $hero }}" @if($heroSet) srcset="{{ $heroSet }}" @endif
                 sizes="(max-width: 1024px) 100vw, 960px"
                 alt="{{ $post->cover_alt }}"
                 @if($heroSize) width="{{ $heroSize[0] }}" height="{{ $heroSize[1] }}" @endif
                 fetchpriority="high" decoding="async"
                 class="max-h-[34rem] w-full bg-paper-2 object-cover">
            @if($post->cover_alt)
                <figcaption class="mt-3 font-mono text-[0.7rem] text-ink-soft">{{ $post->cover_alt }}</figcaption>
            @endif
        </figure>
    @endif

    @if(count($toc['items']) > 2)
        <nav aria-label="Contents" class="mt-12 border-y border-paper-3 py-5">
            <p class="biz-label">Contents</p>
            <ol class="mt-3 space-y-1.5">
                @foreach($toc['items'] as $i => $item)
                    <li class="font-sans text-[0.9rem]">
                        <a href="#{{ $item['id'] }}" class="text-ink-body transition-colors hover:text-brand">
                            <span class="mr-2 font-mono text-[0.68rem] text-ink-soft">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $item['text'] }}
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="prose-jp mt-12">{!! $toc['html'] !!}</div>

    {{-- Again at the foot, where a reader who has actually finished it is far
         likelier to pass it on than one who has just arrived. --}}
    <div class="mt-16 flex flex-wrap items-center justify-between gap-6 border-t border-ink-ink pt-8">
        <x-share-row :post="$post" />
        <a href="{{ route('blog.index') }}" class="font-mono text-[0.75rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">
            &larr; All notes
        </a>
    </div>
</article>

@include('blog._related')

@include('blog._comments')

@include('blog._subscribe')
@endsection
