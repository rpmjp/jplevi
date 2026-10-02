{{--
    The staff bar.

    Rendered only for signed in staff, so a reader's page is byte for byte what
    it was before. WordPress puts its bar over everyone's view and pays for it
    in weight on every page; this one costs a reader nothing because it is not
    there.
--}}
@auth
    @php($staff = auth()->user()->hasAnyRole(['admin', 'editor', 'author']))
@endauth

<div class="border-b border-paper-3 bg-paper">
    <div class="mx-auto flex max-w-biz flex-wrap items-center gap-x-5 gap-y-2 px-6 py-2.5 sm:px-10">
        @guest
            <a href="{{ route('sign-in') }}"
               class="ml-auto border border-brand bg-brand px-4 py-1.5 font-mono text-[0.66rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                Sign in
            </a>
        @else
            @if($staff)
                {{-- Standing on a post: the shortest path from spotting a typo
                     to fixing it. --}}
                @if(! empty($post))
                    <a href="{{ route('filament.admin.resources.posts.edit', $post) }}"
                       class="border border-brand bg-brand px-3.5 py-1.5 font-mono text-[0.66rem] font-semibold uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                        Edit this post
                    </a>

                    @if($post->status !== 'published')
                        <span class="border border-ember px-2.5 py-1 font-mono text-[0.62rem] uppercase tracking-label text-ember">
                            {{ $post->status }}
                        </span>
                    @endif
                @endif

                <a href="{{ route('filament.admin.resources.posts.create') }}"
                   class="font-mono text-[0.66rem] uppercase tracking-label text-ink-body transition-colors hover:text-brand">New post</a>

                <a href="{{ route('filament.admin.resources.categories.index') }}"
                   class="font-mono text-[0.66rem] uppercase tracking-label text-ink-body transition-colors hover:text-brand">Categories</a>

                @php($pending = \App\Models\Comment::where('status', 'pending')->count())
                <a href="{{ route('filament.admin.resources.comments.index') }}"
                   class="font-mono text-[0.66rem] uppercase tracking-label transition-colors {{ $pending > 0 ? 'text-brand' : 'text-ink-body hover:text-brand' }}">
                    Comments{{ $pending > 0 ? ' ('.$pending.')' : '' }}
                </a>

                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="ml-auto border border-ink-ink px-3.5 py-1.5 font-mono text-[0.66rem] uppercase tracking-label text-ink-ink transition-colors hover:border-brand hover:text-brand">
                    Dashboard
                </a>
            @endif

            {{-- The same avatar the bylines use, so a photograph appears here
                 too once one is set, rather than initials in two styles. --}}
            <a href="{{ route('account.show') }}" title="{{ auth()->user()->name }}"
               class="{{ ($staff ?? false) ? '' : 'ml-auto' }} flex items-center transition-opacity hover:opacity-80">
                <x-avatar :user="auth()->user()" :size="28" />
                <span class="sr-only">{{ auth()->user()->name }}</span>
            </a>

            <form method="post" action="{{ route('social.logout') }}">
                @csrf
                <button class="font-mono text-[0.66rem] uppercase tracking-label text-ink-soft transition-colors hover:text-brand">Out</button>
            </form>
        @endguest
    </div>
</div>
