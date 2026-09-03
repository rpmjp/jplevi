<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageIngest;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;

/**
 * Demo content, so the feed and the article page can be judged with something
 * in them.
 *
 * Idempotent by slug, so running it twice does not produce six posts. Covers
 * are generated rather than downloaded: a seeder that reaches out to the
 * network fails on a machine that has none, and stock photography on a
 * technical blog looks like stock photography.
 *
 *   php artisan db:seed --class=TechUpdatesSeeder
 *
 * To take it all back out again, the three slugs are the-mcp-servers-we-actually-kept,
 * small-models-got-good-enough and structured-outputs-retired-our-parser.
 */
class TechUpdatesSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::firstWhere('email', 'vicekartel@gmail.com') ?? User::firstOrFail();

        $topics = collect([
            'Tech updates' => 'What changed this month, and whether it changes what we build.',
            'Machine learning' => null,
            'Engineering' => 'Notes from the inside of the work.',
        ])->mapWithKeys(fn ($intro, $name) => [
            $name => Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name] + ($intro ? ['intro' => $intro] : []),
            ),
        ]);

        $this->portrait($author);

        foreach ($this->posts() as $i => $entry) {
            $post = Post::withTrashed()->firstOrNew(['slug' => $entry['slug']]);

            $post->fill([
                'user_id' => $author->id,
                'title' => $entry['title'],
                'excerpt' => $entry['excerpt'],
                'meta_description' => $entry['excerpt'],
                'body' => $entry['body'],
                'status' => 'published',
                'published_at' => now()->subDays(3 * $i + 1)->setTime(9, 30),
                'audience' => $entry['audience'],
                'comments_open' => true,
                // Demo content has no business being announced anywhere.
                'share_socially' => false,
                'social_message' => $entry['social'],
                'cover_alt' => $entry['cover_alt'],
            ]);

            if (blank($post->cover_path)) {
                $post->cover_path = $this->cover($entry['slug']);
            }

            $post->deleted_at = null;
            $post->save();

            $post->categories()->sync(
                collect($entry['topics'])->map(fn ($name) => $topics[$name]->id)->all(),
            );

            $this->discuss($post, $entry['comments']);
        }
    }

    /**
     * The author's own photograph, taken from the site repository.
     *
     * The cutout on the company page is the same person at the same crop, so
     * there is no reason to ask for it twice. Skipped silently if the file is
     * not there, because the seeder has to work from a checkout of the blog
     * alone. The framing lives in AvatarImport, shared with avatar:set.
     */
    private function portrait(User $author): void
    {
        $source = base_path('../public/robert-jean-pierre.png');

        if (filled($author->avatar_path) || ! is_file($source)) {
            return;
        }

        app(\App\Services\AvatarImport::class)->forUser($author, $source);
    }

    /**
     * A generated cover.
     *
     * A grid of blocks in the site palette, with the arrangement decided by a
     * hash of the slug, so each post gets its own and gets the same one every
     * time. Pushed through ImageIngest rather than written straight to disk, so
     * the seeded posts exercise the same resizing and cropping path as a real
     * upload and prove the srcset works.
     */
    private function cover(string $slug): string
    {
        $palette = ['#0B0B0C', '#1B3EF0', '#5A76FF', '#E4572E', '#F5F3EF', '#E2DED5'];

        $image = app(ImageManager::class)->createImage(1800, 1200)->fill('#0B0B0C');

        // Deterministic, and independent of the machine's random seed.
        mt_srand(crc32($slug));

        $columns = 9;
        $rows = 6;
        $cell = 200;

        for ($x = 0; $x < $columns; $x++) {
            for ($y = 0; $y < $rows; $y++) {
                if (mt_rand(0, 100) > 42) {
                    continue;
                }

                $colour = $palette[mt_rand(0, count($palette) - 1)];
                $span = mt_rand(0, 100) > 78 ? 2 : 1;

                $left = $x * $cell;
                $top = $y * $cell;

                $image->drawRectangle(function (RectangleFactory $rect) use ($left, $top, $cell, $span, $colour) {
                    $rect->at($left, $top)
                        ->size($cell * $span, $cell * $span)
                        ->background($colour);
                });
            }
        }

        // One rule in brand blue across the lower third, so every cover reads as
        // belonging to the same publication however the blocks fall.
        $image->drawRectangle(fn (RectangleFactory $rect) => $rect->at(0, 1080)->size(1800, 24)->background('#1B3EF0'));

        mt_srand();

        $file = tempnam(sys_get_temp_dir(), 'cover').'.png';
        $image->encode(new PngEncoder())->save($file);

        try {
            return app(ImageIngest::class)->store(
                new UploadedFile($file, basename($file), 'image/png', test: true),
                'covers',
            )['path'];
        } finally {
            @unlink($file);
        }
    }

    /**
     * Comment threads.
     *
     * Approved on the way in, because the point of seeding them is to see the
     * thread rendered. The people are fake and their addresses are on
     * example.com, which is reserved for exactly this and can never receive
     * mail by accident.
     */
    private function discuss(Post $post, array $thread): void
    {
        if ($post->comments()->exists()) {
            return;
        }

        foreach ($thread as $entry) {
            $at = $this->within($post, $entry['hours']);

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => $this->commenter($entry['from'])->id,
                'body' => $entry['body'],
                'status' => 'approved',
                'created_at' => $at,
                'updated_at' => $at,
            ]);

            foreach ($entry['replies'] ?? [] as $reply) {
                $replyAt = $this->within($post, $entry['hours'] + $reply['hours']);

                Comment::create([
                    'post_id' => $post->id,
                    'parent_id' => $comment->id,
                    'user_id' => $this->commenter($reply['from'])->id,
                    'body' => $reply['body'],
                    'status' => 'approved',
                    'created_at' => $replyAt,
                    'updated_at' => $replyAt,
                ]);
            }
        }
    }

    /**
     * A comment time that is after the post and before now.
     *
     * The offsets in the copy describe a thread that unfolds over a couple of
     * days, which runs past the present on the most recent post. Left alone the
     * page cheerfully reports that somebody replied twelve hours from now.
     */
    private function within(Post $post, int $hours): \Illuminate\Support\Carbon
    {
        $at = $post->published_at->copy()->addHours($hours);

        return $at->isFuture() ? now()->subMinutes(random_int(5, 90)) : $at;
    }

    private function commenter(string $name): User
    {
        return User::firstOrCreate(
            ['email' => Str::slug($name).'@example.com'],
            ['name' => $name, 'password' => Hash::make(Str::random(32))],
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function posts(): array
    {
        return require database_path('seeders/data/tech-updates.php');
    }
}
