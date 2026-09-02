<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves media from the private disk.
 *
 * Everything is re-encoded on the way in, to WebP for the site and JPEG for the
 * link preview crop, so this only ever emits those two content types. The path
 * is constrained by the route pattern rather than taken whole, which is what
 * keeps a crafted request from walking out of the folder.
 */
class MediaController extends Controller
{
    public function __invoke(Request $request, string $directory, string $file): Response
    {
        $path = "{$directory}/{$file}";

        abort_unless(Storage::disk('media')->exists($path), 404);

        // From the extension, which the route pattern has already constrained
        // to one of two known values, so this can never echo a caller's string.
        $type = str_ends_with($file, '.jpg') ? 'image/jpeg' : 'image/webp';

        return response(Storage::disk('media')->get($path), 200, [
            'Content-Type' => $type,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
