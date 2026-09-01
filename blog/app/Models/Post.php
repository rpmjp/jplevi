<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'body', 'cover_path', 'cover_alt',
        'status', 'published_at', 'meta_title', 'meta_description', 'canonical_url', 'comments_open', 'social_message', 'share_socially', 'audience',
    ];

    /**
     * Defaults that match the migration.
     *
     * Eloquent does not read database defaults back after an insert, so a
     * freshly created model would see null for these and behave as though they
     * were off. Declaring them here keeps the object and the row in agreement.
     */
    protected $attributes = [
        'status' => 'draft',
        'audience' => 'both',
        'comments_open' => true,
        'share_socially' => true,
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'comments_open' => 'boolean',
            'share_socially' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Any change to a post can alter the index, the post itself, a tag
        // listing and the feed, so the page cache is dropped wholesale rather
        // than trying to work out which pages went stale.
        static::saved(fn () => \App\Http\Middleware\CacheResponse::flush());

        // Share once, when a post first becomes publicly visible. Editing a
        // published post must not fire it again.
        static::saved(function (Post $post) {
            $becameLive = $post->wasChanged(['status', 'published_at'])
                && $post->status === 'published'
                && $post->published_at !== null
                && ! $post->published_at->isFuture();

            if ($becameLive && ! $post->socialPosts()->where('status', 'sent')->exists()) {
                \App\Jobs\ShareToSocial::dispatch($post);
            }
        });
        static::deleted(fn () => \App\Http\Middleware\CacheResponse::flush());

        // A revision records what the post was, not what it is about to be, so
        // restoring one puts back the version that existed before this save.
        static::updating(function (Post $post) {
            if (! $post->isDirty(['title', 'excerpt', 'body'])) {
                return;
            }

            Revision::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'title' => $post->getOriginal('title'),
                'excerpt' => $post->getOriginal('excerpt'),
                'body' => $post->getOriginal('body'),
                'created_at' => now(),
            ]);

            $keep = $post->revisions()->limit(Revision::KEEP_PER_POST)->pluck('id');
            Revision::where('post_id', $post->id)->whereNotIn('id', $keep)->delete();
        });

        static::saving(function (Post $post) {
            $post->slug ??= Str::slug($post->title);
            $post->preview_token ??= (string) Str::uuid();
            $post->reading_minutes = self::readingMinutes($post->body ?? '');
        });

        // A slug change would strand every link already pointing at the old
        // URL, so the old path is kept and redirected rather than dropped.
        static::updating(function (Post $post) {
            if ($post->isDirty('slug') && filled($post->getOriginal('slug'))) {
                Redirect::updateOrCreate(
                    ['from' => '/blog/'.$post->getOriginal('slug')],
                    ['to' => '/blog/'.$post->slug, 'status' => 301],
                );
            }
        });
    }

    /**
     * What the index shows.
     *
     * Everything above a read more break, if the author placed one, because a
     * written opening reads better than a sentence cut off mid word. Falls back
     * to the excerpt, then to a trim of the body.
     */
    public function lead(): string
    {
        if (str_contains((string) $this->body, '<!--more-->')) {
            return Str::before($this->body, '<!--more-->');
        }

        return filled($this->excerpt)
            ? '<p>'.e($this->excerpt).'</p>'
            : Str::limit(strip_tags((string) $this->body), 220);
    }

    /** Roughly 220 words a minute, floored at one. */
    public static function readingMinutes(string $html): int
    {
        $words = str_word_count(strip_tags($html));

        return max(1, (int) ceil($words / 220));
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function socialPosts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function revisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Revision::class)->latest('created_at');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** Live to the public: published, and not dated in the future. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
