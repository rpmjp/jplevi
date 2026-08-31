<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Broadcast extends Model
{
    protected $fillable = ['user_id', 'subject', 'preheader', 'intro', 'body', 'status', 'sent_at', 'recipients'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withPivot('position')->orderBy('position');
    }
}
