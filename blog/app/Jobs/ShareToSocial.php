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
 *
 * Worth being precise about what this produces, because it is not the same
 * thing the share buttons on a post produce. Attaching the image makes this a
 * native image post: the writing runs first, folds behind a "more" link, and
 * the picture sits under it full width. Handing a network a bare URL instead,
 * which is all a share button can do, gets a link card with a thumbnail beside
 * a title and a domain. Only the first shape is ours to decide, and this is
 * where it is decided.
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
                        // The 1.91:1 crop, not the article hero. It is the
                        // shape every feed lays an image out at, so the post
                        // arrives framed the way it was cropped rather than
                        // the way the network decided to trim it. With no
                        // cover there is no image, and the network falls back
                        // to a link card built from the URL in the text.
                        'mediaUrls' => array_filter([\App\Models\Rendition::social($this->post->cover_path)]),
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

    /**
     * Roughly where a feed puts the "more" link.
     *
     * LinkedIn folds a post at about this point and the others are close
     * enough. Anything past it is only read by somebody who has already decided
     * to expand, so the opening has to carry the post on its own.
     */
    private const FOLD = 200;

    private function message(): string
    {
        $written = trim((string) $this->post->social_message);
        $url = route('blog.show', $this->post);

        if (filled($written)) {
            return str_contains($written, $url) ? $written : $written."\n\n".$url;
        }

        // Fallback only. The excerpt reads better than a bare headline.
        $lead = $this->post->excerpt ?: $this->post->title;

        return self::hook($lead)."\n\n".$url;
    }

    /**
     * The opening, cut where a reader would not notice the cut.
     *
     * Ends on a full stop where there is one before the fold, because a
     * sentence that finishes reads as writing and an ellipsis mid-clause reads
     * as a truncated database field. Falls back to a word boundary.
     */
    private static function hook(string $lead): string
    {
        $lead = trim(preg_replace('/\s+/', ' ', strip_tags($lead)));

        if (mb_strlen($lead) <= self::FOLD) {
            return $lead;
        }

        $window = mb_substr($lead, 0, self::FOLD);

        foreach (['. ', '? ', '! '] as $ending) {
            $at = mb_strrpos($window, $ending);

            // Only if the sentence is long enough to be worth reading on its
            // own. Cutting at the first full stop can leave three words.
            if ($at !== false && $at > self::FOLD / 2) {
                return mb_substr($window, 0, $at + 1);
            }
        }

        return Str::limit($window, self::FOLD - 1, '…');
    }
}
