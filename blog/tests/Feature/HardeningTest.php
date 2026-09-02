<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    private function live(): Post
    {
        return Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'A measured note',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_security_headers_are_present_on_public_pages(): void
    {
        $this->live();

        $this->get('/')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_the_policy_refuses_inline_and_foreign_script(): void
    {
        $this->live();

        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    public function test_reads_are_counted_by_day_without_identifying_anyone(): void
    {
        $post = $this->live();

        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh)'])->get('/'.$post->slug);
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh)'])->get('/'.$post->slug);

        $row = PageView::where('post_id', $post->id)->first();

        $this->assertNotNull($row);
        $this->assertSame(2, $row->views);
        // One row for the day, not one per reader.
        $this->assertSame(1, PageView::count());
    }

    public function test_bots_and_prefetches_are_not_counted(): void
    {
        $post = $this->live();

        $this->withHeaders(['User-Agent' => 'Googlebot/2.1'])->get('/'.$post->slug);
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Sec-Purpose' => 'prefetch'])->get('/'.$post->slug);

        $this->assertSame(0, PageView::count());
    }

    public function test_backup_writes_outside_the_web_root(): void
    {
        $this->artisan('blog:backup')->assertSuccessful();

        $this->assertDirectoryExists(storage_path('backups'));
        $this->assertStringNotContainsString(public_path(), storage_path('backups'));
    }
}
