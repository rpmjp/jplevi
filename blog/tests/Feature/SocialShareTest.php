<?php

namespace Tests\Feature;

use App\Jobs\ShareToSocial;
use App\Models\Post;
use App\Models\Rendition;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\ImageIngest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SocialShareTest extends TestCase
{
    use RefreshDatabase;

    private function draft(array $attrs = []): Post
    {
        return Post::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'title' => 'Retrieval, and what it costs',
            'excerpt' => 'What a document search system actually takes to build.',
            'body' => '<p>Body.</p>',
            'status' => 'draft',
        ], $attrs));
    }

    public function test_publishing_queues_a_share_but_editing_afterwards_does_not(): void
    {
        Queue::fake();

        $post = $this->draft();
        Queue::assertNothingPushed();

        $post->update(['status' => 'published', 'published_at' => now()->subMinute()]);
        Queue::assertPushed(ShareToSocial::class, 1);

        $post->update(['title' => 'A better headline']);
        Queue::assertPushed(ShareToSocial::class, 1);
    }

    public function test_a_scheduled_post_is_not_shared_before_its_time(): void
    {
        Queue::fake();

        $this->draft(['status' => 'published', 'published_at' => now()->addDay()]);

        Queue::assertNothingPushed();
    }

    public function test_it_posts_to_each_channel_and_records_the_outcome(): void
    {
        config(['social.endpoint' => 'https://example.test/post', 'social.channels' => ['linkedin', 'bluesky']]);
        Http::fake(['example.test/*' => Http::response(['id' => 'remote-123'], 200)]);

        $post = $this->draft(['status' => 'published', 'published_at' => now()->subMinute()]);
        (new ShareToSocial($post))->handle();

        $this->assertSame(2, SocialPost::where('status', 'sent')->count());
        $this->assertSame('remote-123', SocialPost::first()->remote_id);
    }

    public function test_a_failing_network_is_recorded_and_does_not_throw(): void
    {
        config(['social.endpoint' => 'https://example.test/post', 'social.channels' => ['linkedin']]);
        Http::fake(['example.test/*' => Http::response('rate limited', 429)]);

        $post = $this->draft(['status' => 'published', 'published_at' => now()->subMinute()]);
        (new ShareToSocial($post))->handle();

        $record = SocialPost::first();
        $this->assertSame('failed', $record->status);
        $this->assertNotEmpty($record->error);
    }

    public function test_the_same_article_is_never_posted_twice_to_one_network(): void
    {
        config(['social.endpoint' => 'https://example.test/post', 'social.channels' => ['linkedin']]);
        Http::fake(['example.test/*' => Http::response(['id' => 'x'], 200)]);

        $post = $this->draft(['status' => 'published', 'published_at' => now()->subMinute()]);

        (new ShareToSocial($post))->handle();
        (new ShareToSocial($post))->handle();

        Http::assertSentCount(1);
    }

    public function test_the_written_message_wins_over_the_excerpt_and_always_carries_the_link(): void
    {
        config(['social.endpoint' => 'https://example.test/post', 'social.channels' => ['linkedin']]);
        Http::fake(['example.test/*' => Http::response(['id' => 'x'], 200)]);

        $post = $this->draft([
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'social_message' => 'Most people asking for a chatbot want a search box.',
        ]);

        (new ShareToSocial($post))->handle();

        $message = SocialPost::first()->message;
        $this->assertStringContainsString('Most people asking for a chatbot', $message);
        $this->assertStringContainsString(route('blog.show', $post), $message);
    }

    public function test_opting_a_post_out_shares_nothing(): void
    {
        config(['social.endpoint' => 'https://example.test/post']);
        Http::fake();

        $post = $this->draft(['status' => 'published', 'published_at' => now()->subMinute(), 'share_socially' => false]);
        (new ShareToSocial($post))->handle();

        Http::assertNothingSent();
    }

    /* ---- What the post actually looks like in a feed ------------------- */

    /*
     * The distinction these guard is between a native image post, which is what
     * attaching the picture produces and what this job is for, and a link card,
     * which is all a share button on the page can ever produce.
     */

    private function live(array $attrs = []): Post
    {
        config(['social.endpoint' => 'https://example.test/post', 'social.channels' => ['linkedin']]);
        Http::fake(['example.test/*' => Http::response(['id' => 'x'], 200)]);

        return $this->draft(array_merge([
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ], $attrs));
    }

    public function test_the_picture_sent_is_the_crop_every_feed_lays_out_at(): void
    {
        Storage::fake('media');

        $post = $this->live([
            'cover_path' => app(ImageIngest::class)->store(
                UploadedFile::fake()->image('cover.jpg', 1800, 1200), 'covers',
            )['path'],
        ]);

        (new ShareToSocial($post))->handle();

        Http::assertSent(function ($request) use ($post) {
            // The 1.91:1 crop, not the article hero: attaching it is what makes
            // this an image post rather than a link card.
            return $request['mediaUrls'] === [Rendition::social($post->cover_path)]
                && str_contains($request['mediaUrls'][0], '-social.jpg');
        });
    }

    public function test_the_opening_finishes_a_sentence_before_the_fold(): void
    {
        $post = $this->live([
            'excerpt' => 'Small models got good enough for the boring jobs, and the bill went with them. '
                .'Classification, extraction, routing and tagging no longer need a frontier model. '
                .'Here is the routing pattern we settled on, and the three places it still fails.',
        ]);

        (new ShareToSocial($post))->handle();

        Http::assertSent(function ($request) use ($post) {
            [$hook] = explode("\n\n", $request['post']);

            $this->assertLessThanOrEqual(200, mb_strlen($hook));
            // A finished sentence, not a clause cut off mid word.
            $this->assertStringEndsWith('.', $hook);
            $this->assertStringNotContainsString('…', $hook);

            return str_ends_with($request['post'], route('blog.show', $post));
        });
    }

    public function test_a_post_with_no_cover_still_goes_out(): void
    {
        (new ShareToSocial($this->live()))->handle();

        // No image means the network builds a link card from the URL in the
        // text, which is a worse post but not a failed one.
        Http::assertSent(fn ($request) => $request['mediaUrls'] === []);
    }
}
