@extends('layouts.blog')
@section('title', 'Privacy')
@section('description', 'What the JP Levi AI notes store about readers, why, and how to have it removed.')

@section('content')
<section class="mx-auto max-w-5xl px-6 py-16 sm:px-10 sm:py-20">
    <p class="biz-label">Legal</p>
    <h1 class="biz-display mt-5 text-[clamp(2.2rem,6vw,4.2rem)]">Privacy</h1>
    <p class="mt-6 font-mono text-[0.78rem] text-ink-soft">
        JP LEVI INC., a New Jersey corporation &middot; hello@jplevi.com &middot; Effective {{ now()->format('j F Y') }}
    </p>

    <div class="prose-jp mt-12">
        <h2>The short version</h2>
        <p>
            Reading this blog stores nothing about you. No cookies are set, no
            advertising identifier is used, and nothing you do here is shared with
            anyone else. You only ever hand over data by choosing to: subscribing to
            the newsletter, or signing in to comment.
        </p>

        <h2>Reading</h2>
        <p>
            We count reads so we know which posts are worth writing more of. A read
            records the page path, the site you arrived from, and the date. Those are
            aggregated into one row per page per day and incremented, so the record is
            a number rather than a trail. There is no cookie, no fingerprint, and no
            identifier that follows you between visits or between pages. Bots and
            browser prefetches are excluded so the numbers mean something.
        </p>

        <h2>The newsletter</h2>
        <p>
            Subscribing stores your email address, optionally your name, the page you
            subscribed from, and the IP address the request came from. The IP is kept
            as evidence of consent and to rate limit abuse of the form.
        </p>
        <p>
            Nothing is sent until you click the confirmation link. An address that never
            confirms is never mailed. Every email carries a one-click unsubscribe, which
            works without signing in and is honoured immediately. The list is never sold,
            rented, shared, or used for anything except these notes.
        </p>
        <p>
            Delivery is handled by Resend, who process the message in order to send it.
            They are a processor acting on our instructions and do not use your address
            for their own purposes.
        </p>

        <h2>Comments</h2>
        <p>
            Commenting requires signing in with Google, GitHub or LinkedIn. We receive
            your name and email address from that provider and nothing else: no contacts,
            no posts, no permission to act on your behalf. We store your comment, the
            time, and the IP address it came from, which is used for rate limiting and
            for blocking abuse.
        </p>
        <p>
            Comments are reviewed before they appear. Your email address is never shown
            publicly.
        </p>

        <h2>Seeing and deleting what we hold</h2>
        <p>
            Signed in, <a href="{{ route('account.show') }}">your account page</a> lists
            everything held about you, and the delete button on it removes your account
            and every comment you have written, immediately and permanently. There is no
            retention period and nobody talks you out of it.
        </p>
        <p>
            To be removed from the newsletter, use the unsubscribe link in any email or
            write to hello@jplevi.com.
        </p>

        <h2>How long things are kept</h2>
        <p>
            Reading counts are aggregate and kept indefinitely, because they identify
            nobody. Subscriber records are kept until you unsubscribe or ask for removal.
            Comments and accounts are kept until you delete them. Database backups are
            kept for fourteen days and then destroyed, so a deletion is fully gone within
            two weeks at the outside.
        </p>

        <h2>Where it lives</h2>
        <p>
            The site and its database run on servers in the United States, operated by an
            infrastructure partner on our behalf. If you are writing from the EU or UK we
            have not appointed an Article 27 representative; write to hello@jplevi.com and
            we will handle requests directly.
        </p>

        <h2>Changes</h2>
        <p>
            If this changes in a way that affects what we collect, the effective date at
            the top changes with it, and subscribers are told in a normal email rather
            than a silent edit.
        </p>
    </div>
</section>
@endsection
