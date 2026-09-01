<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Revision;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function makePost(array $attrs = []): Post
    {
        return Post::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'title' => 'A note',
            'body' => '<p>Original body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ], $attrs));
    }

    public function test_a_category_is_held_out_of_search_until_it_has_enough_posts(): void
    {
        $category = Category::create(['name' => 'Machine learning']);

        for ($i = 1; $i < Category::INDEX_THRESHOLD; $i++) {
            $category->posts()->attach($this->makePost(['title' => "Post {$i}"]));
            $this->assertFalse($category->shouldBeIndexed(), "Should still be held at {$i} posts");
        }

        $category->posts()->attach($this->makePost(['title' => 'The one that tips it']));
        $this->assertTrue($category->shouldBeIndexed());
    }

    public function test_drafts_do_not_count_towards_the_indexing_threshold(): void
    {
        $category = Category::create(['name' => 'Hosting']);

        for ($i = 0; $i < 5; $i++) {
            $category->posts()->attach($this->makePost(['title' => "Draft {$i}", 'status' => 'draft', 'published_at' => null]));
        }

        $this->assertFalse($category->shouldBeIndexed());
    }

    public function test_editing_a_post_records_what_it_was_before(): void
    {
        $post = $this->makePost(['title' => 'First title']);
        $this->assertSame(0, $post->revisions()->count());

        $post->update(['title' => 'Second title', 'body' => '<p>Rewritten.</p>']);

        $revision = $post->revisions()->first();
        $this->assertSame('First title', $revision->title);
        $this->assertSame('<p>Original body.</p>', $revision->body);
        $this->assertSame('Second title', $post->fresh()->title);
    }

    public function test_a_change_that_touches_nothing_written_makes_no_revision(): void
    {
        $post = $this->makePost();
        $post->update(['comments_open' => false]);

        $this->assertSame(0, $post->revisions()->count());
    }

    public function test_revisions_are_pruned_so_the_table_cannot_run_away(): void
    {
        $post = $this->makePost();

        for ($i = 0; $i < Revision::KEEP_PER_POST + 8; $i++) {
            $post->update(['body' => "<p>Version {$i}.</p>"]);
        }

        $this->assertSame(Revision::KEEP_PER_POST, $post->revisions()->count());
    }

    public function test_trashing_a_post_takes_it_off_the_public_site(): void
    {
        $post = $this->makePost(['title' => 'Published then trashed']);

        $this->get('/blog')->assertSee('Published then trashed');

        $post->delete();

        $this->get('/blog')->assertDontSee('Published then trashed');
        $this->get('/blog/'.$post->slug)->assertNotFound();
        $this->assertStringNotContainsString($post->slug, $this->get('/blog/sitemap.xml')->getContent());
    }

    public function test_a_restored_post_comes_back(): void
    {
        $post = $this->makePost(['title' => 'Back from the dead']);
        $post->delete();
        $post->restore();

        $this->get('/blog')->assertSee('Back from the dead');
    }

    public function test_the_last_admin_cannot_be_removed(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $this->assertTrue(\App\Filament\Admin\Resources\Users\Tables\UsersTable::wouldRemoveLastAdmin($admin));

        $second = User::factory()->create();
        $second->syncRoles(['admin']);

        $this->assertFalse(\App\Filament\Admin\Resources\Users\Tables\UsersTable::wouldRemoveLastAdmin($admin->fresh()));
    }
}
