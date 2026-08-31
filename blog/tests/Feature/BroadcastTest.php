<?php

namespace Tests\Feature;

use App\Jobs\SendBroadcast;
use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    private function subscriber(string $email, bool $confirmed = true, bool $gone = false): Subscriber
    {
        $s = Subscriber::create(['email' => $email]);
        if ($confirmed) $s->confirm();
        if ($gone) $s->unsubscribe();

        return $s->fresh();
    }

    public function test_it_only_mails_people_who_confirmed_and_have_not_left(): void
    {
        Mail::fake();

        $this->subscriber('yes@example.com');
        $this->subscriber('never-confirmed@example.com', confirmed: false);
        $this->subscriber('left@example.com', gone: true);

        $broadcast = Broadcast::create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'A new note',
            'body' => '<p>Something worth reading.</p>',
        ]);

        (new SendBroadcast($broadcast))->handle();

        Mail::assertQueued(BroadcastMail::class, 1);
        Mail::assertQueued(BroadcastMail::class, fn ($m) => $m->hasTo('yes@example.com'));

        $this->assertSame('sent', $broadcast->fresh()->status);
        $this->assertSame(1, $broadcast->fresh()->recipients);
    }

    public function test_every_message_carries_the_one_click_unsubscribe_headers(): void
    {
        $subscriber = $this->subscriber('reader@example.com');
        $broadcast = Broadcast::create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'Headers matter',
            'body' => '<p>Body.</p>',
        ]);

        $headers = (new BroadcastMail($broadcast, $subscriber))->headers();

        $this->assertArrayHasKey('List-Unsubscribe', $headers->text);
        $this->assertSame('List-Unsubscribe=One-Click', $headers->text['List-Unsubscribe-Post']);
        $this->assertStringContainsString($subscriber->unsubscribe_token, $headers->text['List-Unsubscribe']);
    }

    public function test_the_digest_renders_each_post_with_a_link_back(): void
    {
        $author = User::factory()->create();
        $post = \App\Models\Post::create([
            'user_id' => $author->id,
            'title' => 'Retrieval over your own documents',
            'excerpt' => 'What it costs and what it cannot do.',
            'body' => '<p>Body.</p>',
            'cover_path' => 'covers/example.webp',
            'cover_alt' => 'A diagram',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $broadcast = Broadcast::create([
            'user_id' => $author->id,
            'subject' => 'This month',
            'preheader' => 'One new note.',
            'intro' => '<p>Short intro.</p>',
        ]);
        $broadcast->posts()->attach($post);

        $subscriber = $this->subscriber('reader@example.com');
        $html = (new BroadcastMail($broadcast->fresh(), $subscriber))->render();

        $this->assertStringContainsString('Retrieval over your own documents', $html);
        $this->assertStringContainsString('What it costs and what it cannot do.', $html);
        $this->assertStringContainsString(route('blog.show', $post), $html);
        // Cover image must be absolute: email clients resolve nothing.
        $this->assertStringContainsString(url('storage/covers/example.webp'), $html);
        $this->assertStringContainsString('Read it', $html);
        $this->assertStringContainsString($subscriber->unsubscribe_token, $html);
        $this->assertStringContainsString('One new note.', $html);
    }

    public function test_a_send_is_not_repeated(): void
    {
        Mail::fake();
        $this->subscriber('reader@example.com');

        $broadcast = Broadcast::create([
            'user_id' => User::factory()->create()->id,
            'subject' => 'Once only',
            'body' => '<p>Body.</p>',
            'status' => 'sent',
        ]);

        (new SendBroadcast($broadcast))->handle();

        Mail::assertNothingQueued();
    }
}
