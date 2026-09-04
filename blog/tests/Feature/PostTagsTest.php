<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tags, as distinct from categories.
 *
 * A category is a section of the publication: few, picked from a list, worth
 * landing on. A tag is whatever this piece happens to be about, typed while
 * writing. The two are kept apart on purpose, the way WordPress keeps them.
 */
class PostTagsTest extends TestCase
{
    use RefreshDatabase;

    private function publish(string $title = 'Tagged'): Post
    {
        return Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => $title,
            'excerpt' => 'A short summary.',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_typing_a_tag_that_does_not_exist_yet_creates_it(): void
    {
        $post = $this->publish();

        $post->syncTagNames(['Model Context Protocol', 'Evaluation']);

        $this->assertSame(2, Tag::count());
        $this->assertEqualsCanonicalizing(
            ['Model Context Protocol', 'Evaluation'],
            $post->fresh()->tags->pluck('name')->all(),
        );
    }

    public function test_the_same_tag_written_differently_is_still_one_tag(): void
    {
        $first = $this->publish('First');
        $second = $this->publish('Second');

        $first->syncTagNames(['Model Context Protocol']);
        // Different case, stray spaces, and a duplicate within one post.
        $second->syncTagNames(['  model  context  protocol ', 'MODEL CONTEXT PROTOCOL']);

        // Matched on the slug, so these are the same tag rather than three.
        $this->assertSame(1, Tag::count());
        $this->assertSame(1, $second->fresh()->tags->count());
        $this->assertSame(2, Tag::first()->posts()->count());
    }

    public function test_removing_a_tag_from_a_post_detaches_it(): void
    {
        $post = $this->publish();

        $post->syncTagNames(['Kept', 'Dropped']);
        $post->syncTagNames(['Kept']);

        $this->assertSame(['Kept'], $post->fresh()->tags->pluck('name')->all());
        // The tag itself survives: another post may still be using it.
        $this->assertSame(2, Tag::count());
    }

    public function test_a_tag_links_to_an_archive_that_lists_its_posts(): void
    {
        $post = $this->publish('Something about retrieval');
        $post->syncTagNames(['GraphRAG']);

        $other = $this->publish('Unrelated');

        $tag = Tag::firstWhere('slug', 'graphrag');

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('GraphRAG')
            ->assertSee(route('blog.tag', $tag));

        $this->get(route('blog.tag', $tag))
            ->assertOk()
            ->assertSee('Something about retrieval')
            ->assertDontSee('Unrelated');
    }

    public function test_a_tag_archive_is_kept_out_of_search(): void
    {
        $post = $this->publish();
        $post->syncTagNames(['Thin']);

        // A list of posts already indexed somewhere better competes with them
        // for the same words. Readable, followed, not indexed.
        $this->get(route('blog.tag', Tag::firstWhere('slug', 'thin')))
            ->assertOk()
            ->assertSee('noindex, follow', false);
    }

    public function test_retagging_a_post_does_not_leave_the_old_tags_on_screen(): void
    {
        $post = $this->publish('Cached');
        $post->syncTagNames(['Before']);

        // Warm the page cache with the first set.
        $this->get(route('blog.show', $post))->assertOk()->assertSee('Before');

        $post->syncTagNames(['After']);

        // A pivot sync fires no model event, so nothing flushed the cache and
        // the post kept its old tags until the entry expired an hour later.
        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('After')
            ->assertDontSee('Before');
    }

    public function test_the_editor_offers_a_tag_box_with_what_has_been_used_before(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        Tag::create(['name' => 'Already Used']);

        $html = $this->actingAs($admin)
            ->get(route('filament.admin.resources.posts.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Tags', $html);
        // Suggested, so a second post about the same thing reuses the tag
        // rather than inventing a near-duplicate of it.
        $this->assertStringContainsString('Already Used', $html);
    }
}
