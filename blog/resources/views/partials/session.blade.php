{{--
    A thin strip below the header, blog only.

    Kept out of the header itself so that bar stays byte for byte the one every
    other page renders and nothing shifts when moving between Notes and the
    rest of the site.
--}}
<div class="border-b border-paper-3 bg-paper">
    <div class="mx-auto flex max-w-biz items-center justify-end gap-x-5 px-6 py-2.5 sm:px-10">
        @guest
            <a href="{{ route('sign-in') }}"
               class="border border-brand bg-brand px-4 py-1.5 font-mono text-[0.66rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                Sign in
            </a>
        @else
            @if(auth()->user()->hasAnyRole(['admin', 'editor', 'author']))
                <a href="{{ url('/blog/admin') }}"
                   class="border border-brand bg-brand px-4 py-1.5 font-mono text-[0.66rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                    Dashboard
                </a>
            @endif
            <a href="{{ route('account.show') }}" title="{{ auth()->user()->name }}"
               class="flex h-7 w-7 items-center justify-center rounded-full border border-ink-ink font-mono text-[0.58rem] font-semibold uppercase text-ink-ink transition-colors hover:border-brand hover:text-brand">
                {{ Str::of(auth()->user()->name)->explode(' ')->take(2)->map(fn ($w) => Str::substr($w, 0, 1))->implode('') }}
            </a>
            <form method="post" action="{{ route('social.logout') }}">
                @csrf
                <button class="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Out</button>
            </form>
        @endguest
    </div>
</div>
