<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_policies_are_reachable_and_say_what_we_actually_do(): void
    {
        $this->get('/legal/privacy')
            ->assertOk()
            ->assertSee('No cookies are set', false)
            ->assertSee('one-click unsubscribe')
            ->assertSee('fourteen days');

        $this->get('/legal/comment-rules')
            ->assertOk()
            ->assertSee('Comments arrive pending');
    }

    public function test_every_page_links_to_the_policies(): void
    {
        Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'A note', 'body' => '<p>x</p>',
            'status' => 'published', 'published_at' => now()->subHour(),
        ]);

        $this->get('/')
            ->assertSee(route('legal.privacy'), false)
            ->assertSee(route('legal.moderation'), false);
    }

    public function test_the_signup_and_comment_forms_point_at_the_policies(): void
    {
        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Another note', 'body' => '<p>x</p>',
            'status' => 'published', 'published_at' => now()->subHour(),
        ]);

        $html = $this->get('/'.$post->slug)->getContent();

        $this->assertStringContainsString(route('legal.privacy'), $html);
        $this->assertStringContainsString(route('legal.moderation'), $html);
    }
}
