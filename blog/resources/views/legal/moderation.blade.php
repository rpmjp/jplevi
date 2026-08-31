@extends('layouts.blog')
@section('title', 'Comment rules')
@section('description', 'How comments are moderated on the JP Levi AI notes.')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-16 sm:px-10 sm:py-20">
    <p class="biz-label">Legal</p>
    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.2rem)]">Comment rules</h1>

    <div class="prose-jp mt-12">
        <h2>Everything is read before it appears</h2>
        <p>
            Comments arrive pending and a person approves them. That is slower than
            posting straight through, and it is the reason the comments here are worth
            reading.
        </p>

        <h2>What gets through</h2>
        <p>
            Disagreement, corrections, better answers than the one in the post, and
            questions. Telling us we are wrong is welcome, particularly with a reason.
        </p>

        <h2>What does not</h2>
        <ul>
            <li>Personal attacks, harassment, or anything aimed at a person rather than an argument</li>
            <li>Marketing, link dropping, and anything written to rank rather than to be read</li>
            <li>Content that is unlawful, infringing, or discloses someone else's private information</li>
            <li>Machine-written filler posted to establish a presence</li>
        </ul>

        <h2>Blocking</h2>
        <p>
            Repeated abuse gets an address blocked. Blocked comments are dropped without
            notice, because telling someone they are blocked usually just produces a
            second account.
        </p>

        <h2>Your comment stays yours</h2>
        <p>
            You keep whatever rights you have in what you write. Posting it here grants
            permission to display it alongside the post, nothing more. Delete your account
            and your comments go with it: see
            <a href="{{ route('legal.privacy') }}">privacy</a>.
        </p>

        <h2>Comments close eventually</h2>
        <p>
            Older posts have comments closed. A thread that has been quiet for a long time
            attracts spam far more reliably than it attracts conversation.
        </p>
    </div>
</section>
@endsection
