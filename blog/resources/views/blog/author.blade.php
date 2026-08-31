@extends('layouts.blog')
@section('title', $user->name)
@section('description', 'Posts by '.$user->name.' on the JP Levi AI notes.')

@section('content')
<section class="mx-auto max-w-5xl px-6 pb-10 pt-14 sm:px-10 sm:pt-20">
    <p class="biz-label">Author</p>
    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.4rem)]">{{ $user->name }}</h1>
    <p class="mt-7 font-mono text-[0.82rem] text-ink-soft">
        {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
    </p>
</section>

<nav aria-label="Posts by this author" class="border-t border-ink">
    <ul>
        @foreach($posts as $post)
            <li class="border-b border-paper-4">
                <a href="{{ route('blog.show', $post) }}"
                   class="group grid grid-cols-[4.5rem_minmax(0,1fr)_auto] items-center gap-x-5 px-6 py-5 transition-colors hover:bg-night sm:px-10">
                    <span class="font-mono text-[0.68rem] text-ink-soft transition-colors group-hover:text-brand-soft">{{ $post->published_at?->format('d M y') }}</span>
                    <span class="font-display text-[1.05rem] font-bold uppercase leading-tight tracking-tight2 text-ink transition-colors group-hover:text-paper sm:text-[1.2rem]">{{ $post->title }}</span>
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
