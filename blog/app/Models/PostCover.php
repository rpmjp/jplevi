<?php

namespace App\Models;

/**
 * Absolute URLs for a post's cover image.
 *
 * Email clients do not resolve relative paths, so anything that appears in a
 * newsletter has to be a full URL to a publicly reachable file.
 */
class PostCover
{
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : url('storage/'.ltrim($path, '/'));
    }
}
