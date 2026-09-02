<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Rendition;
use App\Models\User;
use App\Services\ImageIngest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The featured image, from upload to link preview.
 *
 * Three things are worth guarding here, because all three fail silently. A
 * missing rendition only shows up as a blurry thumbnail on a retina screen. A
 * missing og:image only shows up when somebody shares the post and it arrives
 * as a bare link. And a broken preview URL cannot be seen from the site at all,
 * because the site never loads that particular file.
 */
class FeaturedImageTest extends TestCase
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

    /** Uploads a real image and returns what goes in the cover_path column. */
    private function upload(int $width = 1800, int $height = 1200): string
    {
        return app(ImageIngest::class)->store(
            UploadedFile::fake()->image('cover.jpg', $width, $height),
            'covers',
        )['path'];
    }

    public function test_an_upload_produces_every_width_and_the_social_crop(): void
    {
        Storage::fake('media');

        $path = $this->upload();

        foreach (ImageIngest::WIDTHS as $expected) {
            Storage::disk('media')->assertExists("{$path}-{$expected}.webp");
        }

        Storage::disk('media')->assertExists("{$path}-social.webp");
    }

    public function test_the_social_crop_is_exactly_the_size_every_network_unfurls(): void
    {
        Storage::fake('media');

        // Portrait on the way in, so a scaled rendition could not accidentally
        // come out at the right ratio and let a real bug through.
        $path = $this->upload(1200, 1800);

        [$width, $height] = getimagesize(
            Storage::disk('media')->path("{$path}-social.webp"),
        );

        $this->assertSame([1200, 630], [$width, $height]);
    }

    public function test_srcset_offers_only_widths_that_were_actually_written(): void
    {
        Storage::fake('media');

        // Smaller than the two largest widths, which are therefore skipped
        // rather than upscaled. Offering the browser an upscale as a bigger
        // file is how srcset makes a page slower and blurrier at once.
        $path = $this->upload(900, 600);

        $srcset = Rendition::srcset($path);

        $this->assertStringContainsString('400w', $srcset);
        $this->assertStringContainsString('800w', $srcset);
        $this->assertStringNotContainsString('1200w', $srcset);
        $this->assertStringNotContainsString('1600w', $srcset);
    }

    public function test_a_post_with_a_cover_previews_as_a_large_card(): void
    {
        Storage::fake('media');

        $post = $this->publish('Has a cover', [
            'cover_path' => $this->upload(),
            'cover_alt' => 'A described photograph.',
        ]);

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertStringContainsString('-social.webp', $html);
        $this->assertStringContainsString('content="summary_large_image"', $html);
        $this->assertStringContainsString('content="1200"', $html);
        $this->assertStringContainsString('content="630"', $html);
        $this->assertStringContainsString('A described photograph.', $html);
    }

    public function test_the_preview_image_is_reachable_without_signing_in(): void
    {
        Storage::fake('media');

        $post = $this->publish('Reachable', ['cover_path' => $this->upload()]);

        // A crawler fetches this URL with no cookies. If the media route ever
        // ends up behind auth, every link preview breaks and nothing on the
        // site would show it.
        $this->get(Rendition::social($post->cover_path))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');
    }

    public function test_a_post_without_a_cover_asks_for_the_small_card(): void
    {
        $post = $this->publish('No cover at all');

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $this->assertStringNotContainsString('property="og:image"', $html);
        $this->assertStringContainsString('name="twitter:card" content="summary"', $html);
    }

    public function test_keep_reading_fills_up_even_with_nothing_in_common(): void
    {
        $post = $this->publish('The one being read');
        $post->categories()->attach(Category::create(['name' => 'Alone']));

        // Five others, none sharing the category.
        foreach (range(1, 5) as $i) {
            $this->publish("Unrelated {$i}");
        }

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $this->assertStringContainsString('Keep reading', $html);

        $shown = collect(range(1, 5))->filter(
            fn ($i) => str_contains($html, "Unrelated {$i}"),
        );

        // Four unrelated posts filled the row, which also proves the post did
        // not offer itself one of the slots.
        $this->assertCount(4, $shown, 'The row should hold four cards, not one.');
    }

    public function test_the_share_links_point_at_the_post(): void
    {
        $post = $this->publish('Worth passing on');

        $html = $this->get(route('blog.show', $post))->assertOk()->getContent();

        $encoded = rawurlencode(route('blog.show', $post));

        foreach (['twitter.com/intent/tweet', 'facebook.com/sharer', 'linkedin.com/sharing', 'reddit.com/submit'] as $network) {
            $this->assertStringContainsString($network, $html);
        }

        $this->assertStringContainsString($encoded, $html);
    }
}
