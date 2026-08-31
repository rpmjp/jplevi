<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = ['value', 'type', 'reason'];

    public static function blocks(?string $email, ?string $ip): bool
    {
        return self::query()
            ->where(fn ($q) => $q->where(['type' => 'email', 'value' => $email])
                ->orWhere(fn ($w) => $w->where('type', 'ip')->where('value', $ip)))
            ->exists();
    }
}
