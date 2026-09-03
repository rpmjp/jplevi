<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_the_dashboard_link_is_not_doubled_under_a_subdirectory_app_url(): void
    {
        // The app is mounted at /blog in production, so a hardcoded
        // url('/blog/admin') produced /blog/blog/admin. Using the panel's own
        // route name means the path is generated, never assembled.
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $html = $this->actingAs($admin)->get('/')->getContent();

        $this->assertStringNotContainsString('/blog/blog/', $html);
        $this->assertStringContainsString(route('filament.admin.pages.dashboard'), $html);
    }

    public function test_the_blog_home_answers_on_both_paths_the_mount_produces(): void
    {
        // Every URL under the mount reaches Laravel with /blog stripped, except
        // the home page, which the web server answers through DirectoryIndex
        // and which arrives as "blog". A redirect used to sit on that path, so
        // clicking Notes in the header sent readers to the site root.
        foreach (['/', '/blog'] as $path) {
            $this->get($path)->assertOk()->assertSee('Working notes', false);
        }
    }

    public function test_an_old_doubled_post_url_still_finds_its_post(): void
    {
        $post = \App\Models\Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Moved',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $this->get('/blog/'.$post->slug)
            ->assertRedirect('/'.$post->slug);
    }

    public function test_a_link_the_index_prints_is_a_link_the_app_answers_on(): void
    {
        // The bug this guards was not a broken link in a template; it was every
        // blog route sitting one level deeper than the mount, so the pages
        // pointed at /blog/blog/{slug} and the posts 404'd. Nothing that reads
        // a single template catches that. Following the link does.
        $post = \App\Models\Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Round trip',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#href="[^"]*/round-trip"#', $html);

        preg_match('#href="([^"]*/round-trip)"#', $html, $matches);

        $this->get($matches[1])->assertOk()->assertSee('Round trip');
    }

    public function test_readers_never_see_the_dashboard_link(): void
    {
        $reader = User::factory()->create();
        $reader->syncRoles(['subscriber']);

        $this->actingAs($reader)->get('/')->assertDontSee('Dashboard');
    }
}
