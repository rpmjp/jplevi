<!DOCTYPE html>
<html lang="en" class="bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Notes') | JP Levi AI</title>
    <meta name="description" content="@yield('description', 'Notes on AI, machine learning, and building software for small businesses.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="alternate" type="application/rss+xml" title="JP Levi AI" href="{{ route('blog.feed') }}">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Saira:wght@400;700;800;900&family=Archivo+Narrow:wght@400;600;700;800;900&family=Space+Grotesk:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-paper font-sans text-ink-body antialiased">

{{--
    The site's chrome, rebuilt in Blade. The static export renders this from
    React components; the markup and classes are kept in step deliberately so
    the blog reads as the same site rather than a bolted-on subdirectory.
--}}

@include('partials.site-rail')

<div class="relative z-10 flex min-h-screen flex-col bg-paper xl:pl-[5.75rem]">
    @include('partials.site-header')

    @include('partials.session')

    <main class="flex-1">@yield('content')</main>

    <footer class="mt-24 bg-night px-6 py-14 text-paper sm:px-10">
        <div class="mx-auto max-w-biz">
            <div class="flex flex-wrap items-end justify-between gap-8 border-b border-white/15 pb-8">
                <p class="font-display text-[clamp(1.6rem,4vw,2.6rem)] font-black uppercase leading-[0.95] tracking-tight3 text-paper">
                    AI that works for<br>your business.
                </p>
                <div class="flex flex-col gap-2">
                    <a href="mailto:hello@jplevi.com" class="font-mono text-[0.8rem] text-paper/70 transition-colors hover:text-paper">hello@jplevi.com</a>
                    <a href="tel:+19293564644" class="font-display text-[1.2rem] font-bold tracking-tight2 text-paper transition-colors hover:text-brand-soft">(929) 356-4644</a>
                </div>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-between gap-x-8 gap-y-4">
                <nav class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    <a href="/services/" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Services</a>
                    <a href="{{ route('blog.index') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Notes</a>
                    <a href="/hosting/" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Hosting</a>
                    <a href="/about/" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Company</a>
                    <a href="/contact/" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Contact</a>
                </nav>
                <nav class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    <a href="{{ route('legal.privacy') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Privacy</a>
                    <a href="{{ route('legal.moderation') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper">Comment rules</a>
                    <a href="{{ route('blog.feed') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper">RSS</a>
                    @guest
                        <a href="{{ route('sign-in') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper xl:hidden">Sign in</a>
                    @else
                        <a href="{{ route('account.show') }}" class="biz-label !text-paper/55 transition-colors hover:!text-paper xl:hidden">Account</a>
                    @endguest
                </nav>
            </div>
            <p class="mt-8 font-mono text-[0.66rem] uppercase tracking-label text-paper/40">
                JP LEVI INC. &middot; a New Jersey corporation &middot; North Brunswick, NJ
            </p>
        </div>
    </footer>
</div>

</body>
</html>
