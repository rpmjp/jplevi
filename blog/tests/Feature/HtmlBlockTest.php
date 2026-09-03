<?php

namespace Tests\Feature;

use App\Filament\RichBlocks\HtmlBlock;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Raw HTML in a post.
 *
 * Two things are being guarded. That the markup reaches the page as written,
 * which is the whole point of the block, and that the ability to write it stays
 * with administrators, which is the only thing standing between it and every
 * author on the site.
 */
class HtmlBlockTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $user->syncRoles([$role]);

        return $user;
    }

    public function test_the_markup_reaches_the_page_exactly_as_written(): void
    {
        $markup = '<div class="ledger"><table><tr><th>Model</th><td>f1 0.94</td></tr></table></div>';

        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'With raw markup',
            'body' => "<p>Before.</p>{$markup}<p>After.</p>",
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        // Rendered, not escaped into visible angle brackets.
        $this->assertStringContainsString($markup, $html);
        $this->assertStringNotContainsString('&lt;div class=&quot;ledger&quot;', $html);
    }

    public function test_a_post_written_with_blocks_actually_renders_on_the_page(): void
    {
        // The shape the editor really stores: a placeholder div carrying the
        // block's settings, not finished HTML. Rendering the column raw printed
        // the placeholder, which is to say printed nothing, so a post built out
        // of blocks came out as a headline with an empty page under it and a
        // reading time of one minute.
        $written = '<h2>Confusion matrix</h2><p>'.str_repeat('word ', 900).'</p>';

        $placeholder = '<div data-type="customBlock" data-id="html" data-config="'
            .e(json_encode(['html' => $written])).'"></div>';

        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Built from blocks',
            'excerpt' => 'The standfirst.',
            'body' => $placeholder,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $this->assertStringContainsString('Confusion matrix', $html);
        $this->assertStringNotContainsString('data-type="customBlock"', $html);

        // Reading time is measured on what was rendered. Measured on the stored
        // placeholder, strip_tags leaves nothing and every post reads as one
        // minute however long it is.
        $this->assertGreaterThan(1, $post->fresh()->reading_minutes);
    }

    public function test_a_post_written_as_plain_html_is_not_reparsed(): void
    {
        // The renderer drops anything outside the editor's own schema, which
        // for hand-written HTML means every aside and figure vanishing. Bodies
        // that are already finished HTML are passed through untouched.
        $body = '<aside class="callout">A note.</aside><figure><blockquote>Quoted.</blockquote></figure>';

        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Written by hand',
            'body' => $body,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $this->assertStringContainsString('<aside class="callout">', $html);
        $this->assertStringContainsString('<figure>', $html);
    }

    public function test_only_an_administrator_is_offered_the_block(): void
    {
        foreach (['admin' => true, 'editor' => false, 'author' => false] as $role => $offered) {
            $html = $this->actingAs($this->user($role))
                ->get(route('filament.admin.resources.posts.create'))
                ->assertOk()
                ->getContent();

            $offered
                ? $this->assertStringContainsString('Custom HTML', $html, "An admin should be offered the block.")
                : $this->assertStringNotContainsString('Custom HTML', $html, "A {$role} should not be offered the block.");
        }
    }

    public function test_markup_the_browser_will_refuse_is_called_out_while_writing(): void
    {
        // The content security policy blocks all three of these, and it blocks
        // them silently. Saying so in the editor is the only place an author
        // finds out before the page is live.
        $this->assertNotEmpty(HtmlBlock::deadOnArrival('<script>alert(1)</script>'));
        $this->assertNotEmpty(HtmlBlock::deadOnArrival('<button onclick="go()">Go</button>'));
        $this->assertNotEmpty(HtmlBlock::deadOnArrival('<iframe src="https://example.com/x"></iframe>'));

        // Ordinary markup, and the two frames the policy does permit.
        $this->assertEmpty(HtmlBlock::deadOnArrival('<div class="grid"><p>Fine.</p></div>'));
        $this->assertEmpty(HtmlBlock::deadOnArrival('<iframe src="https://player.vimeo.com/video/1"></iframe>'));
    }

    public function test_the_preview_shows_the_result_and_the_source(): void
    {
        $preview = HtmlBlock::toPreviewHtml(['html' => '<p class="lede">Hello</p>']);

        // Rendered, so it can be judged by eye.
        $this->assertStringContainsString('<p class="lede">Hello</p>', $preview);
        // And escaped inside the source view, so it can be read as code.
        $this->assertStringContainsString('&lt;p class=&quot;lede&quot;&gt;', $preview);
        $this->assertStringContainsString('<summary', $preview);
    }
}
