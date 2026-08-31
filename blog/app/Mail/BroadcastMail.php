<?php

namespace App\Mail;

use App\Models\Broadcast;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Broadcast $broadcast,
        public Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->broadcast->subject);
    }

    /**
     * The headers that decide whether this reaches an inbox.
     *
     * Gmail, Yahoo and Microsoft require one click unsubscribe on bulk mail and
     * enforce it at the SMTP level: fail and the message is rejected outright
     * rather than filtered. List-Unsubscribe-Post is what makes the client
     * offer its own unsubscribe button instead of the reader hitting "spam".
     */
    public function headers(): Headers
    {
        $url = route('newsletter.unsubscribe', $this->subscriber->unsubscribe_token);

        return new Headers(text: [
            'List-Unsubscribe' => "<{$url}>",
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            'List-Id' => 'JP Levi AI notes <notes.jplevi.com>',
        ]);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.broadcast', with: [
            'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->unsubscribe_token),
        ]);
    }
}
