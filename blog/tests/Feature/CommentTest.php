<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'A note worth arguing with',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    private function reader(): User
    {
        $u = User::factory()->create();
        $u->syncRoles(['subscriber']);

        return $u->fresh();
    }

    public function test_comments_arrive_pending_and_are_not_shown_until_approved(): void
    {
        $this->actingAs($this->reader())
            ->post(route('comments.store', $this->post), ['body' => 'This is wrong, and here is why.'])
            ->assertSessionHas('comment_status');

        $comment = Comment::first();
        $this->assertSame('pending', $comment->status);

        $this->get('/'.$this->post->slug)->assertDontSee('This is wrong, and here is why.');

        $comment->update(['status' => 'approved']);
        $this->get('/'.$this->post->slug)->assertSee('This is wrong, and here is why.');
    }

    public function test_signed_out_readers_cannot_post(): void
    {
        $this->post(route('comments.store', $this->post), ['body' => 'Drive-by.'])
            ->assertRedirect();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_a_blocked_address_is_silently_dropped(): void
    {
        $reader = $this->reader();
        Block::create(['value' => $reader->email, 'type' => 'email', 'reason' => 'Repeated abuse']);

        $this->actingAs($reader)
            ->post(route('comments.store', $this->post), ['body' => 'Back again.'])
            // Same message as success, so blocking is not something to route around.
            ->assertSessionHas('comment_status');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_replies_never_nest_more_than_one_level(): void
    {
        $reader = $this->reader();

        $top = Comment::create(['post_id' => $this->post->id, 'user_id' => $reader->id, 'body' => 'Top level', 'status' => 'approved']);
        $reply = Comment::create(['post_id' => $this->post->id, 'user_id' => $reader->id, 'parent_id' => $top->id, 'body' => 'A reply', 'status' => 'approved']);

        $this->actingAs($reader)->post(route('comments.store', $this->post), [
            'body' => 'Replying to the reply',
            'parent_id' => $reply->id,
        ]);

        // Attaches to the thread root, not to the reply.
        $this->assertSame($top->id, Comment::latest('id')->first()->parent_id);
    }

    public function test_closed_comments_are_refused(): void
    {
        $this->post->update(['comments_open' => false]);

        $this->actingAs($this->reader())
            ->post(route('comments.store', $this->post), ['body' => 'Too late.'])
            ->assertForbidden();
    }

    public function test_the_honeypot_stops_a_bot(): void
    {
        $this->actingAs($this->reader())
            ->post(route('comments.store', $this->post), ['body' => 'Buy things', 'website' => 'http://spam'])
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('comments', 0);
    }
}
