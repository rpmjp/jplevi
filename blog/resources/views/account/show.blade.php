@extends('layouts.blog')
@section('title', 'Your account')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-16 sm:px-10 sm:py-20">
    <p class="biz-label">Account</p>
    <h1 class="biz-display mt-5 text-[clamp(2rem,5vw,3.4rem)]">{{ $user->name }}</h1>

    <div class="mt-10 border-t border-paper-4 pt-6">
        <h2 class="biz-label">What we hold</h2>
        <dl class="mt-4 space-y-2 font-mono text-[0.85rem] text-ink-body">
            <div><dt class="inline text-ink-soft">Email:</dt> <dd class="inline">{{ $user->email }}</dd></div>
            <div><dt class="inline text-ink-soft">Name:</dt> <dd class="inline">{{ $user->name }}</dd></div>
            <div><dt class="inline text-ink-soft">Joined:</dt> <dd class="inline">{{ $user->created_at->format('j F Y') }}</dd></div>
            <div><dt class="inline text-ink-soft">Comments:</dt> <dd class="inline">{{ $comments->count() }}</dd></div>
        </dl>
        <p class="mt-4 max-w-prose font-sans text-[0.9rem] text-ink-body">
            That is everything. No tracking profile, no advertising identifier, nothing shared with anyone.
        </p>
    </div>

    @if($comments->isNotEmpty())
        <div class="mt-10 border-t border-paper-4 pt-6">
            <h2 class="biz-label">Your comments</h2>
            <ul class="mt-4 space-y-4">
                @foreach($comments as $comment)
                    <li class="border-t border-paper-3 pt-3">
                        <p class="font-mono text-[0.7rem] text-ink-soft">
                            <a href="{{ route('blog.show', $comment->post) }}#comments" class="text-brand underline underline-offset-4">{{ $comment->post->title }}</a>
                            &middot; {{ $comment->created_at->format('j M Y') }} &middot; {{ $comment->status }}
                        </p>
                        <p class="mt-1.5 whitespace-pre-line font-sans text-[0.9rem] text-ink-body">{{ $comment->body }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-10 border-t border-ember pt-6">
        <h2 class="biz-label !text-ember">Delete everything</h2>
        <p class="mt-3 max-w-prose font-sans text-[0.9rem] text-ink-body">
            Removes your account and every comment you have written. Immediate, and not reversible.
        </p>
        <form method="post" action="{{ route('account.destroy') }}" class="mt-5"
              onsubmit="return confirm('Delete your account and all your comments?')">
            @csrf @method('delete')
            <button class="border border-ember px-5 py-2.5 font-mono text-[0.72rem] uppercase tracking-label text-ember transition-colors hover:bg-ember hover:text-white">
                Delete my account
            </button>
        </form>
    </div>
</section>
@endsection
