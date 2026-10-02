@extends('layouts.blog')
@section('title', 'You are on the list.')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-24 sm:px-10">
    <h1 class="biz-display text-[clamp(2rem,5.5vw,3.6rem)]">You are on the list.</h1>
    <p class="mt-7 max-w-prose font-sans text-[1.05rem] leading-[1.6] text-ink-body">We will send a note when something worth reading goes up. Not often, and never anything we would not want to receive ourselves.</p>
    <a href="{{ route('blog.index') }}" class="mt-10 inline-block font-mono text-[0.75rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">&larr; Back to the notes</a>
</section>
@endsection
