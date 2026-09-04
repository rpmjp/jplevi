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

<div class="mx-auto max-w-5xl px-6 pt-10 sm:px-10">
    @foreach($posts as $entry)
        @include('blog._card', ['post' => $entry])
    @endforeach

    @if($posts->hasPages())
        <div class="py-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
