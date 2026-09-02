<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Redirect;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function userWith(string $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role]);

        return $user->fresh();
    }

    public function test_admin_can_reach_the_post_list_and_editor(): void
    {
        $admin = $this->userWith('admin');

        $this->actingAs($admin)->get('/admin/posts')->assertOk();
        $this->actingAs($admin)->get('/admin/posts/create')->assertOk();
    }

    public function test_subscribers_are_kept_out_of_the_panel(): void
    {
        $panel = app(Panel::class);

        $this->assertFalse($this->userWith('subscriber')->canAccessPanel($panel));
        $this->assertTrue($this->userWith('author')->canAccessPanel($panel));
    }

    public function test_authors_may_write_but_not_edit_others_or_moderate(): void
    {
        $author = $this->userWith('author');

        $this->assertTrue($author->hasPermissionTo('post.create'));
        $this->assertFalse($author->hasPermissionTo('post.update.any'));
        $this->assertFalse($author->hasPermissionTo('comment.moderate'));
    }

    public function test_renaming_a_slug_leaves_a_permanent_redirect(): void
    {
        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'First title',
            'status' => 'draft',
        ]);

        $post->update(['slug' => 'second-title']);

        $this->assertDatabaseHas('redirects', [
            'from' => '/first-title',
            'to' => '/second-title',
            'status' => 301,
        ]);
    }

    public function test_published_scope_excludes_drafts_and_future_posts(): void
    {
        $author = User::factory()->create();
        Post::create(['user_id' => $author->id, 'title' => 'A draft', 'status' => 'draft']);
        Post::create(['user_id' => $author->id, 'title' => 'Future', 'status' => 'published', 'published_at' => now()->addDay()]);
        Post::create(['user_id' => $author->id, 'title' => 'Live', 'status' => 'published', 'published_at' => now()->subHour()]);

        $this->assertSame(1, Post::published()->count());
        $this->assertSame('Live', Post::published()->first()->title);
    }
}
