@extends('layouts.blog')
@section('title', $post->meta_title ?: $post->title)
@section('description', $post->meta_description ?: $post->excerpt)
@section('canonical', $post->canonical_url ?: route('blog.show', $post))

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
<meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article class="mx-auto max-w-5xl px-6 pt-14 sm:px-10 sm:pt-20">
    @if($preview)
        <p class="mb-8 border-l-2 border-ember pl-4 font-mono text-[0.78rem] text-ink-body">
            Preview of an unpublished draft. Anyone with this link can read it.
        </p>
    @endif

    <p class="biz-label">
        {{ $post->published_at?->format('j F Y') ?? 'Unpublished' }}
        &middot; {{ $post->reading_minutes }} min read
    </p>

    <h1 class="biz-display mt-5 max-w-[20ch] text-[clamp(2.2rem,6.5vw,4.6rem)]">{{ $post->title }}</h1>

    @if($post->excerpt)
        <p class="mt-8 max-w-prose border-l-2 border-brand pl-5 font-sans text-[1.1rem] leading-[1.55] text-ink">
            {{ $post->excerpt }}
        </p>
    @endif

    <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-paper-3 pt-6">
        <a href="{{ route('blog.author', $post->author) }}" class="font-mono text-[0.72rem] text-ink-soft transition-colors hover:text-brand">By {{ $post->author->name }}</a>
        @foreach($post->tags as $tag)
            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
               class="border border-paper-3 px-2.5 py-1 font-mono text-[0.68rem] text-ink-body transition-colors hover:border-ink hover:text-brand">
                {{ $tag->name }}
            </a>
        @endforeach
    </div>

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

    <div class="mt-16 border-t border-ink pt-8">
        <a href="{{ route('blog.index') }}" class="font-mono text-[0.75rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">
            &larr; All notes
        </a>
    </div>
</article>

@include('blog._comments')

@include('blog._subscribe')

@if($related->isNotEmpty())
    <section class="mx-auto mt-20 max-w-5xl px-6 sm:px-10">
        <h2 class="biz-label">Related</h2>
        <ul class="mt-5 grid gap-6 border-t border-paper-4 pt-6 sm:grid-cols-2">
            @foreach($related as $other)
                <li>
                    <a href="{{ route('blog.show', $other) }}" class="group block">
                        <p class="font-mono text-[0.66rem] text-ink-soft">{{ $other->published_at?->format('d M y') }}</p>
                        <p class="mt-1.5 font-display text-[1.05rem] font-bold uppercase leading-tight tracking-tight2 text-ink transition-colors group-hover:text-brand">
                            {{ $other->title }}
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif
@endsection
