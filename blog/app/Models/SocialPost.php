<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    protected $fillable = ['post_id', 'channel', 'message', 'status', 'error', 'remote_id', 'posted_at'];

    protected function casts(): array
    {
        return ['posted_at' => 'datetime'];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
