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
        // The app is served from /blog, so APP_URL already carries it and a
        // hardcoded url('/blog/admin') produced /blog/blog/admin. Using the
        // panel's own route name means the path is generated, never assembled.
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $html = $this->actingAs($admin)->get('/blog')->getContent();

        $this->assertStringNotContainsString('/blog/blog/', $html);
        $this->assertStringContainsString(route('filament.admin.pages.dashboard'), $html);
    }

    public function test_readers_never_see_the_dashboard_link(): void
    {
        $reader = User::factory()->create();
        $reader->syncRoles(['subscriber']);

        $this->actingAs($reader)->get('/blog')->assertDontSee('Dashboard');
    }
}
