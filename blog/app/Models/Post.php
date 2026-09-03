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
     * The read more block as the editor stores it.
     *
     * Matched rather than compared, because the attributes on the placeholder
     * are written in whatever order the editor felt like.
     */
    private const READ_MORE_BLOCK = '/<div[^>]*data-type="customBlock"[^>]*data-id="read-more"[^>]*><\/div>/i';

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
            // Measured on the rendered markup. The stored form is mostly
            // placeholder divs, and strip_tags leaves nothing of those, so
            // a post built from blocks always came out as one minute.
            $post->reading_minutes = self::readingMinutes(self::render($post->body));
        });

        // A slug change would strand every link already pointing at the old
        // URL, so the old path is kept and redirected rather than dropped.
        static::updating(function (Post $post) {
            if ($post->isDirty('slug') && filled($post->getOriginal('slug'))) {
                // Paths are relative to where the app is mounted, which is
                // /blog in production. Storing the mount in the row would make
                // these break the moment the mount changed.
                Redirect::updateOrCreate(
                    ['from' => '/'.$post->getOriginal('slug')],
                    ['to' => '/'.$post->slug, 'status' => 301],
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
    /**
     * Whether a comment may still be posted.
     *
     * The per post switch, and then the age limit from settings: a thread that
     * has been quiet for months attracts spam far more reliably than it
     * attracts conversation.
     */
    public function acceptsComments(): bool
    {
        if (! $this->comments_open) {
            return false;
        }

        $days = (int) \App\Settings::get('comments_close_after_days');

        if ($days > 0 && $this->published_at && $this->published_at->addDays($days)->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * The post body, as HTML, with every editor block turned into markup.
     *
     * The editor does not store finished HTML. A block is written as a
     * placeholder div carrying its settings, and the renderer is what turns
     * each one back into the markup its class defines. Rendering the raw column
     * instead prints the placeholders, which is to say prints nothing: a post
     * built out of blocks comes out as a headline with an empty page under it.
     *
     * Deliberately unsanitised. The sanitiser strips iframes, which would take
     * the video embeds with it, and it would quietly cut down whatever an
     * administrator wrote in a Custom HTML block, which is the one thing that
     * block exists to allow. What protects this page is the content security
     * policy, which refuses script and foreign frames whatever the markup says,
     * and the fact that only staff can write a body at all. Comments, which are
     * written by the public, are escaped and never go near this path.
     */
    public function renderedBody(): string
    {
        return self::render($this->body);
    }

    /** @internal Shared by the accessor and by the reading time estimate. */
    public static function render(?string $content): string
    {
        if (blank($content)) {
            return '';
        }

        // Only content the editor wrote goes through the editor's renderer.
        //
        // The renderer reparses everything against the editor's own schema and
        // drops what is not in it, which for hand-written HTML means aside and
        // figure elements disappearing: every callout and pull quote in a post
        // written before the editor existed. Those bodies are already finished
        // HTML and need nothing done to them, so they are passed through
        // untouched and only block markup is rendered.
        if (! self::needsRendering($content)) {
            return $content;
        }

        $html = \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content)
            ->customBlocks(\App\Filament\RichBlocks\Blocks::all())
            ->toUnsafeHtml();

        return $html;
    }

    /** Whether this body is editor content rather than finished HTML. */
    private static function needsRendering(string $content): bool
    {
        // A TipTap document, or HTML carrying the editor's block placeholders.
        return str_starts_with(ltrim($content), '{')
            || str_starts_with(ltrim($content), '[')
            || str_contains($content, 'data-type="customBlock"');
    }

    public function lead(): string
    {
        $body = (string) $this->body;

        // Split before rendering, not after. The read more block renders as an
        // HTML comment and the renderer strips comments on its way out, so by
        // the time the markup exists the instruction to stop has gone with it.
        // Splitting the stored content on the block itself survives that.
        if (preg_match(self::READ_MORE_BLOCK, $body)) {
            return self::render((string) preg_split(self::READ_MORE_BLOCK, $body, 2)[0]);
        }

        $rendered = $this->renderedBody();

        // A post written as plain HTML carries the marker literally.
        if (str_contains($rendered, '<!--more-->')) {
            return Str::before($rendered, '<!--more-->');
        }

        return filled($this->excerpt)
            ? '<p>'.e($this->excerpt).'</p>'
            : Str::limit(strip_tags($rendered), 220);
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
