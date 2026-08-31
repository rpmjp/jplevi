<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves media from the private disk.
 *
 * Everything is re-encoded to WebP on the way in, so this only ever emits one
 * content type. The path is constrained by the route pattern rather than taken
 * whole, which is what keeps a crafted request from walking out of the folder.
 */
class MediaController extends Controller
{
    public function __invoke(Request $request, string $directory, string $file): Response
    {
        $path = "{$directory}/{$file}";

        abort_unless(Storage::disk('media')->exists($path), 404);

        return response(Storage::disk('media')->get($path), 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
