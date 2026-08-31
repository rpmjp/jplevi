<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    private function publish(string $title, array $attrs = []): Post
    {
        return Post::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'title' => $title,
            'excerpt' => 'A short summary.',
            'body' => '<p>Body copy.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ], $attrs));
    }

    public function test_index_lists_published_posts_only(): void
    {
        $this->publish('Live one');
        $this->publish('Not yet', ['status' => 'draft', 'published_at' => null]);
        $this->publish('Tomorrow', ['published_at' => now()->addDay()]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Live one')
            ->assertDontSee('Not yet')
            ->assertDontSee('Tomorrow');
    }

    public function test_a_published_post_renders_with_structured_data(): void
    {
        $post = $this->publish('Retrieval over your own documents');

        $this->get('/blog/'.$post->slug)
            ->assertOk()
            ->assertSee('Retrieval over your own documents')
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('min read');
    }

    public function test_a_draft_is_hidden_but_reachable_with_its_preview_token(): void
    {
        $post = $this->publish('Unfinished', ['status' => 'draft', 'published_at' => null]);

        $this->get('/blog/'.$post->slug)->assertNotFound();

        $this->get('/blog/'.$post->slug.'?preview='.$post->preview_token)
            ->assertOk()
            ->assertSee('Preview of an unpublished draft');
    }

    public function test_search_and_tag_filtering_narrow_the_index(): void
    {
        $ml = Tag::create(['name' => 'Machine learning', 'audience' => 'engineers']);
        $a = $this->publish('Forecasting demand');
        $a->tags()->attach($ml);
        $this->publish('Hosting migrations');

        $this->get('/blog?tag=machine-learning')->assertSee('Forecasting demand')->assertDontSee('Hosting migrations');
        $this->get('/blog?q=migrations')->assertSee('Hosting migrations')->assertDontSee('Forecasting demand');
    }

    public function test_feed_and_sitemap_are_valid_xml(): void
    {
        $this->publish('In the feed');

        $feed = $this->get('/blog/feed.xml')->assertOk();
        $this->assertNotFalse(simplexml_load_string($feed->getContent()));

        $map = $this->get('/blog/sitemap.xml')->assertOk();
        $this->assertNotFalse(simplexml_load_string($map->getContent()));
    }
}
