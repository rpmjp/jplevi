<?php

namespace App\Jobs;

use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/**
 * Queues one message per confirmed subscriber.
 *
 * Chunked, because shared hosting runs the queue from cron in short bursts
 * rather than holding a worker open. Each message is its own queued job, so a
 * single bad address cannot stop the send.
 */
class SendBroadcast implements ShouldQueue
{
    use Queueable;

    public function __construct(public Broadcast $broadcast) {}

    public function handle(): void
    {
        if ($this->broadcast->status === 'sent') {
            return;
        }

        $this->broadcast->update(['status' => 'sending']);
        $count = 0;

        Subscriber::mailable()->chunkById(200, function ($subscribers) use (&$count) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new BroadcastMail($this->broadcast, $subscriber));
                $count++;
            }
        });

        $this->broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients' => $count,
        ]);
    }
}
