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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo+Narrow:wght@400;600;700;800;900&family=Archivo:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-paper font-sans text-ink-body antialiased">

{{--
    The site's chrome, rebuilt in Blade. The static export renders this from
    React components; the markup and classes are kept in step deliberately so
    the blog reads as the same site rather than a bolted-on subdirectory.
--}}

{{-- Vertical rail down the left edge, desktop only, as on the static pages. --}}
<div class="pointer-events-none fixed inset-y-0 left-0 z-30 hidden border-r border-paper-3 bg-paper xl:flex xl:w-[5.75rem] xl:flex-col xl:items-center xl:justify-between xl:py-6">
    <span aria-hidden="true" class="biz-rail shrink-0 font-mono text-[0.56rem] uppercase tracking-label text-ink-soft">
        AI Engineering / Software / Infrastructure
    </span>
    <div class="flex shrink-0 flex-col items-center gap-4">
        <a href="tel:+19293564644" class="biz-rail pointer-events-auto shrink-0 font-display text-[1rem] font-bold tracking-tight2 text-ink transition-colors hover:text-brand">
            (929) 356-4644
        </a>
        <a href="/contact/" class="biz-rail pointer-events-auto shrink-0 bg-brand px-2.5 py-3 font-mono text-[0.6rem] font-semibold uppercase tracking-label text-white transition-colors hover:bg-ink">
            Consult with an expert &#8599;
        </a>
    </div>
</div>

<div class="relative z-10 flex min-h-screen flex-col bg-paper xl:pl-[5.75rem]">
    <header class="sticky top-0 z-40 border-b border-paper-3 bg-paper/90 backdrop-blur">
        <div class="mx-auto grid min-h-[76px] max-w-biz grid-cols-[auto_1fr_auto] items-center gap-x-6 px-6 py-4 sm:px-10 lg:h-[88px] lg:py-0">
            <a href="/" aria-label="JP Levi AI home" class="group inline-flex items-center gap-3">
                {{-- Same drawing as the site mark and the favicon. --}}
                <svg viewBox="0 0 36 36" aria-hidden="true" class="h-10 w-10 shrink-0">
                    <path d="M6 20 L18 26.5 L18 6 L30 7.7 L30 18.5 L23.4 17.6 L23.4 26.5"
                          fill="none" stroke="#1877F2" stroke-width="2.4"
                          stroke-linecap="square" stroke-linejoin="miter"/>
                </svg>
                <span class="font-mono text-[0.64rem] font-medium uppercase tracking-label text-ink transition-colors group-hover:text-brand">JP Levi AI</span>
            </a>

            <nav aria-label="Main" class="col-span-3 row-start-2 mt-3 border-t border-paper-3 pt-3 lg:col-span-1 lg:col-start-2 lg:row-start-1 lg:mt-0 lg:justify-self-center lg:border-0 lg:pt-0">
                <ul class="flex flex-wrap items-center gap-x-7 gap-y-2 sm:gap-x-10">
                    @foreach([
                        ['/services/', 'Services', false],
                        [route('blog.index'), 'Notes', true],
                        ['/hosting/', 'Hosting', false],
                        ['/about/', 'Company', false],
                        ['/contact/', 'Contact', false],
                    ] as [$href, $label, $active])
                        <li>
                            <a href="{{ $href }}"
                               @if($active) aria-current="page" @endif
                               class="relative py-2 font-sans text-[0.92rem] transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:bg-brand after:transition-transform hover:text-brand {{ $active ? 'text-ink after:scale-x-100' : 'text-ink-body after:scale-x-0 hover:after:scale-x-100' }}">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="col-start-3 row-start-1 ml-auto flex items-center gap-x-5">
                @guest
                    <a href="{{ route('sign-in') }}"
                       class="border border-brand bg-brand px-4 py-2 font-mono text-[0.68rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                        Sign in
                    </a>
                @else
                    @if(auth()->user()->hasAnyRole(['admin', 'editor', 'author']))
                        <a href="{{ url('/blog/admin') }}"
                           class="border border-brand bg-brand px-4 py-2 font-mono text-[0.68rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                            Dashboard
                        </a>
                    @endif
                    <a href="{{ route('account.show') }}"
                       title="{{ auth()->user()->name }}"
                       class="flex h-8 w-8 items-center justify-center rounded-full border border-ink font-mono text-[0.6rem] font-semibold uppercase text-ink transition-colors hover:border-brand hover:text-brand">
                        {{ Str::of(auth()->user()->name)->explode(' ')->take(2)->map(fn ($w) => Str::substr($w, 0, 1))->implode('') }}
                    </a>
                    <form method="post" action="{{ route('social.logout') }}">
                        @csrf
                        <button class="font-mono text-[0.68rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Out</button>
                    </form>
                @endguest

                <a href="tel:+19293564644" class="font-display text-[1.05rem] font-bold tracking-tight2 text-ink transition-colors hover:text-brand">(929) 356-4644</a>
                <p class="hidden items-center gap-2.5 lg:flex">
                    <span aria-hidden="true" class="inline-block h-2 w-2 rounded-full bg-[#146C33]"></span>
                    <span class="font-sans text-[0.88rem] text-ink-body">Taking on select projects</span>
                </p>
            </div>
        </div>
    </header>

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
