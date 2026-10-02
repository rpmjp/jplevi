@extends('layouts.blog')
@section('title', $tag->name)
@section('description', 'Posts tagged '.$tag->name.' on the JP Levi AI notes.')

@push('head')
    {{--
        Kept out of search deliberately.

        A tag archive is a list of things already indexed somewhere better: the
        posts themselves, and the category the posts sit in. Asking for it to be
        indexed too competes with them for the same words. Readers still get it,
        and the links on it are still followed.
    --}}
    <meta name="robots" content="noindex, follow">
@endpush

@section('content')
<section class="mx-auto max-w-5xl px-6 pb-10 pt-14 sm:px-10 sm:pt-20">
    <p class="biz-label">
        <a href="{{ route('blog.index') }}" class="transition-colors hover:text-brand">Notes</a> / Tagged
    </p>

    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.4rem)]">{{ $tag->name }}</h1>

    <p class="mt-7 font-mono text-[0.8rem] text-ink-soft">
        {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
    </p>
</section>

<div class="mx-auto max-w-5xl px-6 pt-4 sm:px-10">
    @forelse($posts as $entry)
        @include('blog._card', ['post' => $entry])
    @empty
        <p class="border-y border-paper-3 py-16 text-center font-mono text-[0.85rem] text-ink-soft">
            Nothing tagged this yet.
        </p>
    @endforelse

    @if($posts->hasPages())
        <div class="py-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
