@extends('layouts.blog')
@section('title', 'Notes')
@section('description', 'Notes on AI, machine learning, and building software for small businesses.')

@section('content')
<section class="mx-auto max-w-5xl px-6 pt-14 sm:px-10 sm:pt-20">
    <p class="biz-label">Notes</p>
    <h1 class="biz-display mt-5 text-[clamp(2.4rem,7vw,5rem)]">
        Working notes<span class="ml-3 inline-block h-[0.13em] w-[0.13em] rounded-full bg-brand align-baseline"></span>
    </h1>
    <p class="mt-8 max-w-xl border-l-2 border-ink-ink pl-5 font-mono text-[0.9rem] leading-relaxed">
        Machine learning experiments, build write-ups, and opinions about where AI actually helps a
        business. Some of it is for engineers, some for owners.
    </p>

    <form method="get" class="mt-10 flex flex-wrap items-center gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search notes"
               class="w-full max-w-xs border border-paper-4 bg-white/60 px-4 py-2.5 font-sans text-[0.9rem] text-ink-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none">
        @if(request('topic'))<input type="hidden" name="topic" value="{{ request('topic') }}">@endif
        <button class="border border-ink-ink bg-ink-ink px-5 py-2.5 font-mono text-[0.72rem] uppercase tracking-label text-paper transition-colors hover:border-brand hover:bg-brand">Search</button>
        @if(request('q') || request('topic'))
            <a href="{{ route('blog.index') }}" class="font-mono text-[0.72rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Clear</a>
        @endif
    </form>

    {{-- Topics as a tab strip above the feed, which is where a reader looks for
         a way to narrow it down. The current one is underlined rather than
         filled, so the row stays quiet next to the headlines below it. --}}
    @if($topics->isNotEmpty())
        <nav aria-label="Topics" class="mt-10 -mb-px flex gap-6 overflow-x-auto border-b border-paper-3">
            <a href="{{ route('blog.index') }}"
               class="shrink-0 border-b-2 pb-3 font-sans text-[0.9rem] transition-colors {{ request('topic') ? 'border-transparent text-ink-soft hover:text-ink-ink' : 'border-ink-ink font-medium text-ink-ink' }}">
                All
            </a>
            @foreach($topics as $topic)
                <a href="{{ route('blog.topic', $topic) }}"
                   class="shrink-0 border-b-2 border-transparent pb-3 font-sans text-[0.9rem] text-ink-soft transition-colors hover:text-ink-ink">
                    {{ $topic->name }}
                    <span class="ml-1 font-mono text-[0.7rem] text-ink-soft/70">{{ $topic->posts_count }}</span>
                </a>
            @endforeach
        </nav>
    @endif
</section>

<div class="mx-auto max-w-5xl px-6 pt-10 sm:px-10">
    @forelse($posts as $post)
        @include('blog._card', ['post' => $post])
    @empty
        <p class="border-y border-paper-3 py-16 text-center font-mono text-[0.85rem] text-ink-soft">
            Nothing published yet.
        </p>
    @endforelse

    @if($posts->hasPages())
        <div class="py-10">{{ $posts->withQueryString()->links() }}</div>
    @endif
</div>

@include('blog._subscribe')
@endsection
