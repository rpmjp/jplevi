<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

/**
 * Recalculates the reading time on every post.
 *
 * The estimate is worked out when a post is saved, so posts written before the
 * body was rendered correctly still carry the number that was arrived at by
 * counting a placeholder: one minute, whatever their length. Saving each post
 * by hand would fix it and would also stamp a revision on each one for no
 * reason, so this does it directly.
 *
 *   php artisan posts:reading-time
 */
class RecountReadingTime extends Command
{
    protected $signature = 'posts:reading-time {--dry-run : Show what would change and stop}';

    protected $description = 'Recalculate reading time from the rendered body of every post';

    public function handle(): int
    {
        $changed = 0;

        foreach (Post::withTrashed()->cursor() as $post) {
            $was = (int) $post->reading_minutes;
            $now = Post::readingMinutes(Post::render($post->body));

            if ($was === $now) {
                continue;
            }

            $this->line(sprintf('  %-52s %s min -> %s min', \Illuminate\Support\Str::limit($post->title, 50), $was, $now));
            $changed++;

            if ($this->option('dry-run')) {
                continue;
            }

            // Written straight to the column: going through save() would file a
            // revision on every post recording a change nobody made.
            $post->newQuery()->withTrashed()->whereKey($post->id)->update(['reading_minutes' => $now]);
        }

        \App\Http\Middleware\CacheResponse::flush();

        $this->newLine();
        $this->info($changed === 0
            ? 'Every post already reads correctly.'
            : ($this->option('dry-run') ? "{$changed} would change." : "{$changed} updated."));

        return self::SUCCESS;
    }
}
