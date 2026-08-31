@extends('layouts.blog')
@section('title', 'Sign in')
@section('description', 'Sign in to comment on the JP Levi AI notes.')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-20 sm:px-10 sm:py-28">
    <p class="biz-label">Readers</p>
    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.2rem)]">Sign in</h1>
    <p class="mt-8 max-w-prose border-l-2 border-brand pl-5 font-sans text-[1.05rem] leading-[1.55] text-ink">
        Signing in lets you comment. There is no password to set, nothing is posted
        anywhere on your behalf, and you can delete your account and everything in it
        whenever you like.
    </p>

    @error('auth')
        <p class="mt-6 border-l-2 border-ember pl-4 font-mono text-[0.82rem] text-ink-body">{{ $message }}</p>
    @enderror

    <div class="mt-10 flex flex-wrap gap-3">
        @foreach(['google' => 'Google', 'github' => 'GitHub', 'linkedin-openid' => 'LinkedIn'] as $slug => $label)
            <a href="{{ route('social.redirect', $slug) }}"
               class="border border-ink px-5 py-3 font-mono text-[0.75rem] uppercase tracking-label text-ink transition-colors hover:border-brand hover:bg-brand hover:text-white">
                Continue with {{ $label }}
            </a>
        @endforeach
    </div>

    <p class="mt-8 max-w-prose font-mono text-[0.78rem] leading-relaxed text-ink-soft">
        We receive your name and email address from whichever you choose, and nothing
        else. See <a href="{{ route('legal.privacy') }}" class="text-brand underline underline-offset-4">privacy</a>
        and the <a href="{{ route('legal.moderation') }}" class="text-brand underline underline-offset-4">comment rules</a>.
    </p>

    <div class="mt-14 border-t border-paper-3 pt-8">
        <p class="biz-label">Writing here?</p>
        <p class="mt-3 max-w-prose font-sans text-[0.95rem] text-ink-body">
            Authors and editors sign in with an email and password at
            <a href="{{ url('/blog/admin') }}" class="text-brand underline underline-offset-4">the panel</a>.
        </p>
    </div>
</section>
@endsection
