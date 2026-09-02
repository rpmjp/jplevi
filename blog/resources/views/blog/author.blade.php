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

<div class="mx-auto max-w-5xl px-6 pt-10 sm:px-10">
    @foreach($posts as $post)
        @include('blog._card', ['post' => $post])
    @endforeach

    @if($posts->hasPages())
        <div class="py-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
