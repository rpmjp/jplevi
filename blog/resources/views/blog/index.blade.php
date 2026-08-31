@extends('layouts.blog')
@section('title', 'Notes')
@section('description', 'Notes on AI, machine learning, and building software for small businesses.')

@section('content')
<section class="mx-auto max-w-5xl px-6 pb-10 pt-14 sm:px-10 sm:pt-20">
    <p class="biz-label">Notes</p>
    <h1 class="biz-display mt-5 text-[clamp(2.4rem,7vw,5rem)]">
        Working notes<span class="ml-3 inline-block h-[0.13em] w-[0.13em] rounded-full bg-brand align-baseline"></span>
    </h1>
    <p class="mt-8 max-w-xl border-l-2 border-ink pl-5 font-mono text-[0.9rem] leading-relaxed">
        Machine learning experiments, build write-ups, and opinions about where AI actually helps a
        business. Some of it is for engineers, some for owners. The tags say which.
    </p>

    <form method="get" class="mt-10 flex flex-wrap items-center gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search notes"
               class="w-full max-w-xs border border-paper-4 bg-white/60 px-4 py-2.5 font-sans text-[0.9rem] text-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none">
        @if(request('tag'))<input type="hidden" name="tag" value="{{ request('tag') }}">@endif
        <button class="border border-ink bg-ink px-5 py-2.5 font-mono text-[0.72rem] uppercase tracking-label text-paper transition-colors hover:border-brand hover:bg-brand">Search</button>
        @if(request('q') || request('tag'))
            <a href="{{ route('blog.index') }}" class="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Clear</a>
        @endif
    </form>

    @if($tags->isNotEmpty())
        <ul class="mt-6 flex flex-wrap gap-2">
            @foreach($tags as $tag)
                <li>
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                       class="inline-block border px-3 py-1.5 font-mono text-[0.7rem] transition-colors
                              {{ request('tag') === $tag->slug ? 'border-brand bg-brand text-white' : 'border-paper-3 text-ink-body hover:border-ink' }}">
                        {{ $tag->name }}
                        <span class="{{ request('tag') === $tag->slug ? 'text-white/70' : 'text-ink-soft' }}">{{ $tag->posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>

{{-- The specimen table from /services: the whole row is the target. --}}
<nav aria-label="Notes" class="border-t border-ink">
    <ul>
        @forelse($posts as $post)
            <li class="border-b border-paper-4">
                <a href="{{ route('blog.show', $post) }}"
                   class="group grid grid-cols-[4.5rem_minmax(0,1fr)_auto] items-center gap-x-5 px-6 py-5 transition-colors hover:bg-night sm:px-10 lg:grid-cols-[6rem_minmax(0,20rem)_minmax(0,1fr)_auto] lg:gap-x-8">
                    <span class="font-mono text-[0.68rem] text-ink-soft transition-colors group-hover:text-brand-soft">
                        {{ $post->published_at?->format('d M y') ?? 'Draft' }}
                    </span>
                    <span class="font-display text-[1.05rem] font-bold uppercase leading-tight tracking-tight2 text-ink transition-colors group-hover:text-paper sm:text-[1.2rem]">
                        {{ $post->title }}
                    </span>
                    <span class="hidden lg:grid">
                        <span class="[grid-area:1/1] self-center font-mono text-[0.66rem] uppercase tracking-label text-ink-soft transition-opacity duration-200 group-hover:opacity-0">
                            {{ $post->tags->pluck('name')->implode(' &middot; ') ?: 'Untagged' }}
                        </span>
                        <span class="[grid-area:1/1] line-clamp-2 self-center font-sans text-[0.8rem] leading-[1.4] text-paper/70 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                            {{ $post->excerpt }}
                        </span>
                    </span>
                    <span aria-hidden="true" class="font-mono text-[0.9rem] text-ink-soft transition-all group-hover:translate-x-1 group-hover:text-brand-soft">&rarr;</span>
                </a>
            </li>
        @empty
            <li class="px-6 py-16 text-center font-mono text-[0.85rem] text-ink-soft sm:px-10">
                Nothing published yet.
            </li>
        @endforelse
    </ul>
</nav>

@include('blog._subscribe')

@if($posts->hasPages())
    <div class="mx-auto max-w-5xl px-6 py-10 sm:px-10">{{ $posts->withQueryString()->links() }}</div>
@endif
@endsection
