@extends('layouts.blog')
@section('title', $category->name)
@section('description', $category->intro ?: 'Notes on '.$category->name.' from JP Levi AI.')

@push('head')
    @unless($indexed)
        {{-- Too little on it to be worth landing on. Readable, not indexed. --}}
        <meta name="robots" content="noindex, follow">
    @endunless
@endpush

@section('content')
<section class="mx-auto max-w-5xl px-6 pb-10 pt-14 sm:px-10 sm:pt-20">
    <p class="biz-label">
        <a href="{{ route('blog.index') }}" class="transition-colors hover:text-brand">Notes</a>
        @if($category->parent)
            / <a href="{{ route('blog.topic', $category->parent) }}" class="transition-colors hover:text-brand">{{ $category->parent->name }}</a>
        @endif
    </p>

    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.4rem)]">{{ $category->name }}</h1>

    @if($category->intro)
        <p class="mt-8 max-w-prose border-l-2 border-brand pl-5 font-sans text-[1.05rem] leading-[1.55] text-ink-ink">
            {{ $category->intro }}
        </p>
    @endif

    @if($children->isNotEmpty())
        <ul class="mt-8 flex flex-wrap gap-2">
            @foreach($children as $child)
                <li>
                    <a href="{{ route('blog.topic', $child) }}"
                       class="inline-block border border-paper-3 px-3 py-1.5 font-mono text-[0.7rem] text-ink-body transition-colors hover:border-ink-ink hover:text-brand">
                        {{ $child->name }} <span class="text-ink-soft">{{ $child->posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <p class="mt-7 font-mono text-[0.8rem] text-ink-soft">
        {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
    </p>
</section>

<nav aria-label="Posts in this topic" class="border-t border-ink-ink">
    <ul>
        @foreach($posts as $post)
            <li class="border-b border-paper-4">
                <a href="{{ route('blog.show', $post) }}"
                   class="group grid grid-cols-[4.5rem_minmax(0,1fr)_auto] items-center gap-x-5 px-6 py-5 transition-colors hover:bg-night sm:px-10">
                    <span class="font-mono text-[0.68rem] text-ink-soft transition-colors group-hover:text-brand-soft">{{ $post->published_at?->format('d M y') }}</span>
                    <span class="font-display text-[1.05rem] font-bold uppercase leading-tight tracking-tight2 text-ink-ink transition-colors group-hover:text-paper sm:text-[1.2rem]">{{ $post->title }}</span>
                    <span aria-hidden="true" class="font-mono text-[0.9rem] text-ink-soft transition-all group-hover:translate-x-1 group-hover:text-brand-soft">&rarr;</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>

@if($posts->hasPages())
    <div class="mx-auto max-w-5xl px-6 py-10 sm:px-10">{{ $posts->links() }}</div>
@endif
@endsection
