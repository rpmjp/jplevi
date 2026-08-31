<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['path', 'alt', 'caption', 'user_id'];

    protected static function booted(): void
    {
        static::creating(fn (Media $m) => $m->user_id ??= auth()->id());
    }
}
