<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = ['email', 'name', 'source', 'signup_ip'];

    protected $hidden = ['confirm_token', 'unsubscribe_token'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $s) {
            $s->confirm_token ??= Str::random(64);
            $s->unsubscribe_token ??= Str::random(64);
        });
    }

    /** Confirmed, and not since opted out. The only people we may email. */
    public function scopeMailable(Builder $query): Builder
    {
        return $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function confirm(): void
    {
        $this->forceFill([
            'confirmed_at' => $this->confirmed_at ?? now(),
            'unsubscribed_at' => null,
            'confirm_token' => null,
        ])->save();
    }

    public function unsubscribe(): void
    {
        $this->forceFill(['unsubscribed_at' => now()])->save();
    }
}
