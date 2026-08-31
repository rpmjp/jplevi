<!DOCTYPE html>
<html lang="en" class="bg-paper">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Notes') | JP Levi AI</title>
    <meta name="description" content="@yield('description', 'Notes on AI, machine learning, and building software for small businesses.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="alternate" type="application/rss+xml" title="JP Levi AI" href="{{ route('blog.feed') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo+Narrow:wght@400;600;700;800;900&family=Archivo:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap">
    @vite('resources/css/app.css')
    @stack('head')
</head>
<body class="bg-paper text-ink-body font-sans antialiased">

<header class="border-b border-paper-3">
    <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-5 sm:px-10">
        <a href="/" class="font-display text-lg font-black uppercase tracking-tight2 text-ink">JP Levi AI</a>
        <nav class="flex flex-wrap items-center gap-x-7 gap-y-2 font-sans text-[0.92rem]">
            <a href="/services/" class="text-ink-body transition-colors hover:text-brand">Services</a>
            <a href="/hosting/" class="text-ink-body transition-colors hover:text-brand">Hosting</a>
            <a href="{{ route('blog.index') }}" class="text-ink underline decoration-brand underline-offset-[6px]">Notes</a>
            <a href="/contact/" class="text-ink-body transition-colors hover:text-brand">Contact</a>
        </nav>
    </div>
</header>

<main>@yield('content')</main>

<footer class="mt-24 border-t border-paper-3">
    <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-8 sm:px-10">
        <p class="biz-label">JP LEVI INC. &middot; North Brunswick, NJ</p>
        <a href="{{ route('blog.feed') }}" class="biz-label transition-colors hover:text-brand">RSS</a>
    </div>
</footer>

</body>
</html>
