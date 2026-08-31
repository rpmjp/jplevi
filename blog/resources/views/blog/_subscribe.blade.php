<section class="mx-auto mt-20 max-w-5xl px-6 sm:px-10">
    <div class="border border-ink-ink p-8 sm:p-10">
        <h2 class="biz-display text-[clamp(1.5rem,3.5vw,2.2rem)]">Get the notes by email</h2>
        <p class="mt-4 max-w-prose font-sans text-[0.98rem] leading-[1.6] text-ink-body">
            New posts when they go up. You confirm first, you can leave in one click, and the list
            is never sold, shared, or used for anything else.
        </p>

        @if(session('status'))
            <p class="mt-6 border-l-2 border-brand pl-4 font-mono text-[0.82rem] text-ink-body">{{ session('status') }}</p>
        @endif
        @error('email')
            <p class="mt-6 border-l-2 border-ember pl-4 font-mono text-[0.82rem] text-ink-body">{{ $message }}</p>
        @enderror

        <form method="post" action="{{ route('newsletter.subscribe') }}" class="mt-7 flex flex-wrap gap-3">
            @csrf
            <input type="hidden" name="source" value="{{ $source ?? 'blog' }}">
            <input type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true"
                   class="absolute left-[-9999px] h-px w-px opacity-0">
            <label for="sub-email" class="sr-only">Email address</label>
            <input id="sub-email" type="email" name="email" required placeholder="you@company.com"
                   class="w-full max-w-xs border border-paper-4 bg-white/60 px-4 py-3 font-sans text-[0.92rem] text-ink-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none">
            <button class="border border-brand bg-brand px-6 py-3 font-mono text-[0.72rem] uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">
                Subscribe
            </button>
        </form>
    </div>
</section>
