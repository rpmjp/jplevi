<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageCacheTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(string $title): Post
    {
        return Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => $title,
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_anonymous_pages_are_cached_and_flushed_on_change(): void
    {
        $this->makePost('First note');

        $this->get('/')->assertHeader('X-Cache', 'miss');
        $this->get('/')->assertHeader('X-Cache', 'hit');

        // Publishing must not leave readers looking at yesterday's index.
        $this->makePost('Second note');

        $this->get('/')->assertHeader('X-Cache', 'miss')->assertSee('Second note');
    }

    public function test_draft_previews_are_never_cached(): void
    {
        $draft = $this->makePost('Hidden');
        $draft->update(['status' => 'draft', 'published_at' => null]);

        $this->get('/hidden?preview='.$draft->preview_token)
            ->assertOk()
            ->assertHeaderMissing('X-Cache');
    }

    public function test_signed_in_readers_are_not_served_a_cached_page(): void
    {
        $this->makePost('Public note');
        $this->get('/');

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertHeaderMissing('X-Cache');
    }
}
