<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Category;
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

    public function test_the_index_shows_a_posts_categories_not_a_dead_relation(): void
    {
        $category = Category::create(['name' => 'Machine learning']);
        $this->publish('Categorised')->categories()->attach($category);
        $this->publish('Bare');

        $html = $this->get('/blog')->getContent();

        $this->assertStringContainsString('Machine learning', $html);
        // The row used to read a relation that is always empty now.
        $this->assertStringNotContainsString('Untagged', $html);

        // A post with no category simply has no "In ..." clause in its byline.
        // The old row printed "Uncategorised" there, which told a reader nothing
        // and put a word on screen that no author had written.
        $this->assertStringNotContainsString('Uncategorised', $html);
        $this->assertStringContainsString('Bare', $html);
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

    public function test_search_and_topic_filtering_narrow_the_index(): void
    {
        $ml = Category::create(['name' => 'Machine learning']);
        $a = $this->publish('Forecasting demand');
        $a->categories()->attach($ml);
        $this->publish('Hosting migrations');

        $this->get('/blog?topic=machine-learning')->assertSee('Forecasting demand')->assertDontSee('Hosting migrations');
        $this->get('/blog?q=migrations')->assertSee('Hosting migrations')->assertDontSee('Forecasting demand');
    }

    public function test_an_archive_lists_its_own_posts_and_is_held_out_of_search_while_thin(): void
    {
        $ml = Category::create(['name' => 'Machine learning', 'intro' => 'Notes on models.']);
        $this->publish('In the topic')->categories()->attach($ml);
        $this->publish('Somewhere else');

        $response = $this->get('/blog/topic/machine-learning')
            ->assertOk()
            ->assertSee('In the topic')
            ->assertDontSee('Somewhere else')
            ->assertSee('Notes on models.');

        // One post is not a page worth landing on.
        $this->assertStringContainsString('noindex', $response->getContent());
    }

    public function test_an_archive_with_enough_posts_is_indexed(): void
    {
        $ml = Category::create(['name' => 'Hosting']);

        for ($i = 0; $i < \App\Models\Category::INDEX_THRESHOLD; $i++) {
            $this->publish("Post {$i}")->categories()->attach($ml);
        }

        $this->assertStringNotContainsString('noindex', $this->get('/blog/topic/hosting')->getContent());
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
