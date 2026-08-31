{{-- The one thing the static site has no equivalent of. Sits inside the
     header's existing right-hand cluster, so nothing about the spacing of the
     rest of the bar changes. --}}
{{-- Signed out, the header carries nothing at all: it stays byte for byte the
     header every other page renders. Readers reach sign in from the comment
     box on a post, which is the only place they need it. --}}
@guest
@else
    @if(auth()->user()->hasAnyRole(['admin', 'editor', 'author']))
        <a href="{{ url('/blog/admin') }}"
           class="border border-brand bg-brand px-4 py-2 font-mono text-[0.68rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
            Dashboard
        </a>
    @endif
    <a href="{{ route('account.show') }}" title="{{ auth()->user()->name }}"
       class="flex h-8 w-8 items-center justify-center rounded-full border border-ink-ink font-mono text-[0.6rem] font-semibold uppercase text-ink-ink transition-colors hover:border-brand hover:text-brand">
        {{ Str::of(auth()->user()->name)->explode(' ')->take(2)->map(fn ($w) => Str::substr($w, 0, 1))->implode('') }}
    </a>
    <form method="post" action="{{ route('social.logout') }}">
        @csrf
        <button class="font-mono text-[0.68rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Out</button>
    </form>
@endguest
