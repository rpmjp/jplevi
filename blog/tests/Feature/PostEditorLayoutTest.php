<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The shape of the writing screen.
 *
 * These panels used to sit behind tabs, which meant the featured image could
 * only be set by leaving the editor. Anything that belongs to finishing a post
 * has to be visible while the post is being written, so this asserts the
 * sidebar is actually there rather than trusting the layout not to drift back.
 */
class PostEditorLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        return $admin;
    }

    public function test_the_editor_carries_a_featured_image_panel_beside_the_writing(): void
    {
        $html = $this->actingAs($this->admin())
            ->get(route('filament.admin.resources.posts.create'))
            ->assertOk()
            ->getContent();

        foreach (['Featured image', 'Alt text', 'Categories', 'Publish', 'Excerpt'] as $panel) {
            $this->assertStringContainsString($panel, $html, "The editor is missing the {$panel} panel.");
        }
    }

    public function test_the_slug_prefix_is_where_the_post_actually_lives(): void
    {
        $admin = $this->admin();

        $post = \App\Models\Post::create([
            'user_id' => $admin->id,
            'title' => 'Where does this live',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->actingAs($admin)
            ->get(route('filament.admin.resources.posts.edit', $post))
            ->assertOk()
            ->getContent();

        // The prefix was assembled by hand as url('/blog').'/', which appended
        // a second /blog to an APP_URL that already carried one and told the
        // author their post lived somewhere it did not. Generated from the
        // router, the prefix and the real URL cannot disagree.
        $expected = rtrim(route('blog.index'), '/').'/';

        $this->assertStringContainsString(e($expected), $html);
        $this->assertStringStartsWith($expected, route('blog.show', $post));
        $this->assertStringNotContainsString('/blog/blog/', $html);
    }

    public function test_the_editor_opens_on_an_existing_post_with_a_cover(): void
    {
        $admin = $this->admin();

        // The upload is not repeated here; what matters is that a stored cover
        // path does not blow up the form when it is read back for editing.
        $post = \App\Models\Post::create([
            'user_id' => $admin->id,
            'title' => 'Already has a cover',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'cover_path' => 'covers/01ABCDEFGHJKMNPQRSTVWXYZ00',
            'cover_alt' => 'Described.',
        ]);

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.posts.edit', $post))
            ->assertOk()
            ->assertSee('Featured image');
    }
}
