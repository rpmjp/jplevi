<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_renamed_post_still_answers_on_its_old_url(): void
    {
        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Original heading',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $this->get('/original-heading')->assertOk();

        $post->update(['slug' => 'better-heading']);
        Cache::forget('redirects.map');

        $this->get('/original-heading')
            ->assertStatus(301)
            ->assertRedirect('/better-heading');

        $this->get('/better-heading')->assertOk();
    }

    public function test_an_unknown_url_still_404s(): void
    {
        $this->get('/never-existed')->assertNotFound();
    }
}
