<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Services\ImageIngest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorAvatarTest extends TestCase
{
    use RefreshDatabase;

    private function publish(User $author): Post
    {
        return Post::create([
            'user_id' => $author->id,
            'title' => 'Bylined',
            'excerpt' => 'A short summary.',
            'body' => '<p>Body.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
    }

    public function test_an_author_without_a_photo_gets_initials_rather_than_a_silhouette(): void
    {
        $author = User::factory()->create(['name' => 'Ada Lovelace']);

        $html = $this->get(route('blog.show', $this->publish($author)))->assertOk()->getContent();

        $this->assertStringContainsString('>AL<', $html);
        // Never Gravatar: that hands a third party the email address of
        // everyone who writes or comments, on every page view.
        $this->assertStringNotContainsString('gravatar', strtolower($html));
    }

    public function test_an_uploaded_photo_replaces_the_initials_on_the_post_and_the_feed(): void
    {
        Storage::fake('media');

        $author = User::factory()->create(['name' => 'Ada Lovelace']);

        $author->avatar_path = app(ImageIngest::class)->store(
            UploadedFile::fake()->image('face.jpg', 800, 800),
            'avatars',
            ImageIngest::AVATAR_WIDTHS,
            social: false,
            square: true,
        )['path'];
        $author->save();

        foreach ([route('blog.show', $this->publish($author)), route('blog.index')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertStringContainsString('media/avatars/', $html);
            $this->assertStringNotContainsString('>AL<', $html);
        }
    }

    public function test_an_avatar_is_cropped_square_and_costs_less_than_a_cover(): void
    {
        Storage::fake('media');

        // Deliberately not square. In a circular mask a tall portrait would
        // otherwise have its sides sliced off by the browser rather than by us.
        $path = app(ImageIngest::class)->store(
            UploadedFile::fake()->image('tall.jpg', 600, 1000),
            'avatars',
            ImageIngest::AVATAR_WIDTHS,
            social: false,
            square: true,
        )['path'];

        [$width, $height] = getimagesize(Storage::disk('media')->path("{$path}-192.webp"));
        $this->assertSame($width, $height);

        // No 1.91:1 crop: a face is never a link preview, so generating one
        // would be a file written on every upload and never fetched.
        Storage::disk('media')->assertMissing("{$path}-social.webp");
        Storage::disk('media')->assertMissing("{$path}-1600.webp");
    }
}
