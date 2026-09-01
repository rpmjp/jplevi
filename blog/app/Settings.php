<?php

namespace App;

use Illuminate\Support\Facades\Cache;

/**
 * Settings that are decisions rather than code.
 *
 * Stored as rows and cached, because these are read on nearly every request and
 * change perhaps twice a year. Credentials deliberately stay in the environment:
 * a key in the database is a key one mis-click from being displayed.
 */
class Settings
{
    public const DEFAULTS = [
        'posts_per_page' => 15,
        'feed_full_text' => false,
        'comments_open_by_default' => true,
        'comments_close_after_days' => 0,
        'moderation_email' => '',
        'default_audience' => 'both',
        'site_tagline' => 'Notes on AI, machine learning, and building software for small businesses.',
    ];

    public static function all(): array
    {
        return Cache::rememberForever('settings', function () {
            $stored = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->all();

            return array_merge(self::DEFAULTS, array_map(
                fn ($v) => json_decode($v, true),
                $stored,
            ));
        });
    }

    public static function get(string $key): mixed
    {
        return self::all()[$key] ?? self::DEFAULTS[$key] ?? null;
    }

    public static function put(array $values): void
    {
        foreach ($values as $key => $value) {
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => json_encode($value)],
            );
        }

        Cache::forget('settings');
    }
}
