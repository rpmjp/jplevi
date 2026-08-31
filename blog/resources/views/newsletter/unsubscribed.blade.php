@extends('layouts.blog')
@section('title', 'Unsubscribed.')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-24 sm:px-10">
    <h1 class="biz-display text-[clamp(2rem,5.5vw,3.6rem)]">Unsubscribed.</h1>
    <p class="mt-7 max-w-prose font-sans text-[1.05rem] leading-[1.6] text-ink-body">You will not hear from us again. No confirmation step, no talking you out of it.</p>
    <a href="{{ route('blog.index') }}" class="mt-10 inline-block font-mono text-[0.75rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">&larr; Back to the notes</a>
</section>
@endsection
