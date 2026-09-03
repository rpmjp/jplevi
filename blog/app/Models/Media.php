<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * An image in the library.
 *
 * The row stores the directory and basename of an upload with no width and no
 * extension, the same as a post cover, and every URL is derived from it. That
 * is what makes the direct link stable: it names the image, not one particular
 * rendition of it, so the widths can change without breaking a link somebody
 * has already pasted into a post.
 */
class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'path', 'title', 'alt', 'caption', 'description',
        'original_name', 'width', 'height', 'bytes', 'user_id',
    ];

    protected static function booted(): void
    {
        static::creating(fn (Media $m) => $m->user_id ??= auth()->id());

        // Something to call it by. WordPress does the same on upload: the
        // filename becomes the title, and it is edited afterwards if it is
        // worth editing.
        static::saving(function (Media $m) {
            $m->title = filled($m->title)
                ? $m->title
                : Str::of(pathinfo((string) $m->original_name, PATHINFO_FILENAME))
                    ->replace(['-', '_'], ' ')
                    ->squish()
                    ->title()
                    ->value();
        });
    }

    /** The direct link. What gets pasted into a post or shared. */
    public function url(int $width = 1200): ?string
    {
        return Rendition::url($this->path, $width);
    }

    public function srcset(): ?string
    {
        return Rendition::srcset($this->path);
    }

    /**
     * The markup, ready to paste into a Custom HTML block.
     *
     * Carries the candidate widths and the description, because an image
     * pasted in as a bare src is one that ships the desktop file to a phone
     * and says nothing to a screen reader.
     */
    public function embedCode(): string
    {
        $tag = '<img src="'.e($this->url()).'"';

        if ($set = $this->srcset()) {
            $tag .= "\n     srcset=\"".e($set).'"'
                ."\n     sizes=\"(max-width: 1024px) 100vw, 960px\"";
        }

        $tag .= "\n     alt=\"".e((string) $this->alt).'"';

        if ($this->width && $this->height) {
            $tag .= "\n     width=\"{$this->width}\" height=\"{$this->height}\"";
        }

        $tag .= "\n     loading=\"lazy\" decoding=\"async\">";

        if (blank($this->caption)) {
            return $tag;
        }

        return "<figure>\n  ".str_replace("\n", "\n  ", $tag)
            ."\n  <figcaption>".e($this->caption)."</figcaption>\n</figure>";
    }

    public function dimensions(): ?string
    {
        return $this->width && $this->height ? "{$this->width} × {$this->height}" : null;
    }

    public function fileSize(): ?string
    {
        if (! $this->bytes) {
            return null;
        }

        return $this->bytes >= 1048576
            ? round($this->bytes / 1048576, 1).' MB'
            : round($this->bytes / 1024).' KB';
    }

    /** Uploaded but still undescribed, and therefore invisible to some readers. */
    public function scopeUndescribed(Builder $query): Builder
    {
        // Grouped. A bare orWhere here would escape whatever the caller had
        // already constrained and quietly widen the result.
        return $query->where(fn (Builder $q) => $q->whereNull('alt')->orWhere('alt', ''));
    }

    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
