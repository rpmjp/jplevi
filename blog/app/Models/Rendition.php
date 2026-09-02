<?php

namespace App\Models;

use App\Services\ImageIngest;
use Illuminate\Support\Facades\Storage;

/**
 * URLs for an uploaded image.
 *
 * One value is stored on the post: the directory and basename of the upload,
 * with no width and no extension. Every rendition is derived from it, which is
 * what lets the widths in ImageIngest change without a migration.
 *
 * Two rules are worth knowing. Everything returns an absolute URL, because
 * email clients and link unfurlers do not resolve relative paths. And the
 * social crop is a separate file, not the largest width, because a preview has
 * to be 1.91:1 and an article hero rarely is.
 */
class Rendition
{
    public static function has(?string $path): bool
    {
        return filled($path);
    }

    /**
     * The src attribute: one concrete file, for browsers that ignore srcset
     * and for anything that needs a single URL.
     */
    public static function url(?string $path, int $width = 1200): ?string
    {
        if (blank($path)) {
            return null;
        }

        // An absolute URL, or a legacy value that already names a file, is
        // passed through untouched rather than having a width appended to it.
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (pathinfo($path, PATHINFO_EXTENSION)) {
            return url('storage/'.ltrim($path, '/'));
        }

        return self::rendition($path, self::nearest($path, $width));
    }

    /**
     * The candidate list. The browser picks from it using the sizes attribute,
     * so a phone never downloads the 1600px file to paint a 380px column.
     */
    public static function srcset(?string $path): ?string
    {
        if (blank($path) || str_starts_with((string) $path, 'http') || pathinfo($path, PATHINFO_EXTENSION)) {
            return null;
        }

        $candidates = [];

        foreach (self::available($path) as $width) {
            $candidates[] = self::rendition($path, $width)." {$width}w";
        }

        return $candidates ? implode(', ', $candidates) : null;
    }

    /**
     * The 1200x630 crop that Facebook, X, LinkedIn and Slack unfurl.
     *
     * Falls back to the widest ordinary rendition, so a post whose cover was
     * uploaded before the crop existed still previews with a picture rather
     * than with nothing.
     */
    public static function social(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http') || pathinfo($path, PATHINFO_EXTENSION)) {
            return self::url($path);
        }

        // The current format first, then the one earlier uploads were written
        // in, so a post whose crop predates the change still previews.
        foreach ([ImageIngest::SOCIAL_EXTENSION, 'webp'] as $extension) {
            if (self::exists($file = "{$path}-social.{$extension}")) {
                return self::link($file);
            }
        }

        return self::url($path, 1600);
    }

    /**
     * The real pixel size of the widest rendition.
     *
     * Used for the width and height attributes on the article hero. Without
     * them the page reflows the moment the image arrives, and guessing an
     * aspect ratio is worse than not setting them at all: the browser reserves
     * the wrong box and the reflow happens anyway.
     *
     * @return array{0:int,1:int}|null
     */
    public static function dimensions(?string $path): ?array
    {
        if (blank($path) || str_starts_with($path, 'http') || pathinfo($path, PATHINFO_EXTENSION)) {
            return null;
        }

        $widths = self::available($path);

        if (! $widths) {
            return null;
        }

        $file = Storage::disk('media')->path("{$path}-".end($widths).'.webp');
        $size = @getimagesize($file);

        return $size ? [$size[0], $size[1]] : null;
    }

    /**
     * Widths actually written for this image, smallest first.
     *
     * Both sets are checked because covers and avatars are generated at
     * different widths, and the stored path does not say which it is.
     */
    public static function available(string $path): array
    {
        $candidates = array_unique([...ImageIngest::AVATAR_WIDTHS, ...ImageIngest::WIDTHS]);
        sort($candidates);

        return array_values(array_filter(
            $candidates,
            fn (int $w) => self::exists("{$path}-{$w}.webp"),
        ));
    }

    /** The generated width closest to what the caller asked for, never smaller. */
    private static function nearest(string $path, int $want): int
    {
        $available = self::available($path) ?: ImageIngest::WIDTHS;

        foreach ($available as $width) {
            if ($width >= $want) {
                return $width;
            }
        }

        return (int) end($available);
    }

    private static function rendition(string $path, int $width): string
    {
        return self::link("{$path}-{$width}.webp");
    }

    /**
     * Media lives outside the web root, so it is served by a controller rather
     * than linked to directly. The route splits on the last slash because the
     * pattern keeps directory and filename as separate, constrained segments.
     */
    private static function link(string $file): string
    {
        [$directory, $name] = array_pad(explode('/', $file, 2), 2, null);

        if ($name === null) {
            return url('storage/'.$file);
        }

        return route('media', ['directory' => $directory, 'file' => $name]);
    }

    private static function exists(string $file): bool
    {
        return Storage::disk('media')->exists($file);
    }
}
