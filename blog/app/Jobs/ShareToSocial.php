<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\SocialPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Posts a published article to the configured social channels.
 *
 * Queued so that a network that is down, rate limiting us, or has changed its
 * API overnight can never stop a post from going live. Every attempt is
 * recorded per channel, so a failure is visible and retryable rather than
 * silently absent.
 */
class ShareToSocial implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Post $post, public ?string $only = null) {}

    public function backoff(): array
    {
        return [60, 600, 3600];
    }

    public function handle(): void
    {
        if (! $this->post->share_socially) {
            return;
        }

        $endpoint = config('social.endpoint');
        $channels = $this->only ? [$this->only] : config('social.channels');

        foreach ($channels as $channel) {
            $record = SocialPost::firstOrCreate(
                ['post_id' => $this->post->id, 'channel' => $channel],
                ['message' => $this->message(), 'status' => 'queued'],
            );

            // Already away: never post the same article twice to one network.
            if ($record->status === 'sent') {
                continue;
            }

            if (blank($endpoint)) {
                $record->update(['status' => 'queued', 'error' => 'No social endpoint configured.']);

                continue;
            }

            try {
                $response = Http::withToken(config('social.key'))
                    ->timeout(20)
                    ->post($endpoint, [
                        'platforms' => [$channel],
                        'post' => $record->message,
                        'mediaUrls' => array_filter([\App\Models\PostCover::url($this->post->cover_path)]),
                    ])
                    ->throw();

                $record->update([
                    'status' => 'sent',
                    'posted_at' => now(),
                    'remote_id' => data_get($response->json(), 'id'),
                    'error' => null,
                ]);
            } catch (\Throwable $e) {
                $record->update([
                    'status' => 'failed',
                    'error' => Str::limit($e->getMessage(), 500),
                ]);
            }
        }
    }

    private function message(): string
    {
        $written = trim((string) $this->post->social_message);
        $url = route('blog.show', $this->post);

        if (filled($written)) {
            return str_contains($written, $url) ? $written : $written."\n\n".$url;
        }

        // Fallback only. The excerpt reads better than a bare headline.
        $lead = $this->post->excerpt ?: $this->post->title;

        return Str::limit($lead, 220)."\n\n".$url;
    }
}
