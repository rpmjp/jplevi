<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** Below this, an archive has too little on it to be worth landing on. */
    public const INDEX_THRESHOLD = 3;

    protected $fillable = ['name', 'slug', 'parent_id', 'intro', 'position'];

    protected static function booted(): void
    {
        static::saving(fn (Category $c) => $c->slug ??= Str::slug($c->name));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('name');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Whether search engines should hold this archive.
     *
     * An archive with one post on it is a page whose only purpose is to link to
     * a better page. Indexing those is how WordPress manufactures thin content
     * that competes with the writing.
     */
    public function shouldBeIndexed(): bool
    {
        return $this->posts()->published()->count() >= self::INDEX_THRESHOLD;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
