<section id="comments" class="mx-auto mt-20 max-w-5xl px-6 sm:px-10">
    <div class="border-t border-ink pt-8">
        <h2 class="biz-label">{{ $comments->count() }} {{ Str::plural('comment', $comments->count()) }}</h2>

        @if(session('comment_status'))
            <p class="mt-6 border-l-2 border-brand pl-4 font-mono text-[0.82rem] text-ink-body">{{ session('comment_status') }}</p>
        @endif
        @error('body')
            <p class="mt-6 border-l-2 border-ember pl-4 font-mono text-[0.82rem] text-ink-body">{{ $message }}</p>
        @enderror

        @if($post->comments_open)
            @auth
                <form method="post" action="{{ route('comments.store', $post) }}" class="mt-8">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
                           class="absolute left-[-9999px] h-px w-px opacity-0">
                    <label for="comment-body" class="biz-label">Add to the discussion</label>
                    <textarea id="comment-body" name="body" rows="4" required
                              placeholder="Disagreement is welcome. Rudeness is not."
                              class="mt-3 w-full resize-y border border-paper-4 bg-white/60 px-4 py-3 font-sans text-[0.92rem] leading-relaxed text-ink placeholder:text-ink-soft/70 focus:border-brand focus:outline-none"></textarea>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <button class="border border-brand bg-brand px-5 py-2.5 font-mono text-[0.72rem] uppercase tracking-label text-white transition-colors hover:border-brand-soft hover:bg-brand-soft">Post</button>
                        <span class="font-mono text-[0.72rem] text-ink-soft">
                            as {{ auth()->user()->name }} &middot;
                            <a href="{{ route('legal.moderation') }}" class="underline underline-offset-[4px] hover:text-brand">rules</a> &middot;
                            <button form="logout-form" class="underline underline-offset-[4px] hover:text-brand">sign out</button>
                        </span>
                    </div>
                </form>
                <form id="logout-form" method="post" action="{{ route('social.logout') }}" class="hidden">@csrf</form>
            @else
                <div class="mt-8 border border-paper-4 p-6">
                    <p class="font-sans text-[0.95rem] text-ink-body">
                        Sign in to comment. No password, and nothing is posted anywhere on your behalf.
                        We receive your name and email from the provider, nothing else. See
                        <a href="{{ route('legal.privacy') }}" class="text-brand underline underline-offset-4">privacy</a>
                        and the <a href="{{ route('legal.moderation') }}" class="text-brand underline underline-offset-4">comment rules</a>.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @foreach(['google' => 'Google', 'github' => 'GitHub', 'linkedin-openid' => 'LinkedIn'] as $slug => $label)
                            <a href="{{ route('social.redirect', $slug) }}"
                               class="border border-ink px-4 py-2.5 font-mono text-[0.72rem] uppercase tracking-label text-ink transition-colors hover:border-brand hover:text-brand">
                                Continue with {{ $label }}
                            </a>
                        @endforeach
                    </div>
                    <p class="mt-4 font-mono text-[0.72rem] text-ink-soft">
                        <a href="{{ route('sign-in') }}" class="underline underline-offset-4 hover:text-brand">More about signing in</a>
                    </p>
                </div>
            @endauth
        @else
            <p class="mt-6 font-mono text-[0.82rem] text-ink-soft">Comments are closed on this post.</p>
        @endif

        <ol class="mt-12 space-y-8">
            @foreach($comments as $comment)
                <li class="border-t border-paper-3 pt-5">
                    <p class="font-mono text-[0.7rem] uppercase tracking-label text-ink-soft">
                        {{ $comment->author->name }} &middot; {{ $comment->created_at->diffForHumans() }}
                    </p>
                    <p class="mt-2.5 whitespace-pre-line font-sans text-[0.95rem] leading-relaxed text-ink-body">{{ $comment->body }}</p>

                    @if($comment->replies->isNotEmpty())
                        <ol class="mt-5 space-y-5 border-l-2 border-paper-3 pl-5">
                            @foreach($comment->replies as $reply)
                                <li>
                                    <p class="font-mono text-[0.68rem] uppercase tracking-label text-ink-soft">
                                        {{ $reply->author->name }} &middot; {{ $reply->created_at->diffForHumans() }}
                                    </p>
                                    <p class="mt-2 whitespace-pre-line font-sans text-[0.92rem] leading-relaxed text-ink-body">{{ $reply->body }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</section>
