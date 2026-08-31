<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmSubscription;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // A signup form is a free way to send mail from someone else's domain,
        // so it is rate limited by address before anything is sent.
        $key = 'subscribe:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Too many attempts. Try again shortly.']);
        }

        RateLimiter::hit($key, 3600);

        $data = $request->validate([
            // Format only. A live DNS check would put a network round trip in
            // front of every signup, and a bad domain is better caught by the
            // confirmation email simply never being clicked.
            'email' => ['required', 'email:rfc', 'max:190'],
            'name' => ['nullable', 'string', 'max:120'],
            // Bots fill hidden fields; people do not.
            'company' => ['prohibited'],
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'name' => $data['name'] ?? null,
                'source' => $request->input('source', 'blog'),
                'signup_ip' => $request->ip(),
            ],
        );

        // Already confirmed: say the same thing either way, so the form cannot
        // be used to find out who is on the list.
        if (! $subscriber->confirmed_at) {
            Mail::to($subscriber->email)->queue(new ConfirmSubscription($subscriber));
        }

        return back()->with('status', 'Check your email for a confirmation link.');
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('confirm_token', $token)->firstOrFail();
        $subscriber->confirm();

        return view('newsletter.confirmed');
    }

    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->unsubscribe();

        // One-click unsubscribe posts here. Mail clients expect a 200 and no
        // further interaction, so a POST answers immediately.
        if ($request->isMethod('post')) {
            return response('Unsubscribed', 200);
        }

        return view('newsletter.unsubscribed');
    }
}
