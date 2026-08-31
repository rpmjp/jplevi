<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Full page cache for anonymous readers.
 *
 * Shared hosting time to first byte sits around a second under load, which is
 * above what counts as good and makes a fast Largest Contentful Paint close to
 * impossible. Serving a stored response skips routing, Eloquent and Blade
 * entirely, which is the single biggest lever available here.
 *
 * Only anonymous GETs are cached, so nobody is ever served a page rendered for
 * somebody else. The cache is dropped whenever a post changes.
 */
class CacheResponse
{
    private const TTL_MINUTES = 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldCache($request)) {
            return $next($request);
        }

        $key = 'page:'.sha1($request->fullUrl());

        if ($cached = Cache::get($key)) {
            return response($cached['body'], 200, $cached['headers'])
                ->header('X-Cache', 'hit');
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            Cache::put($key, [
                'body' => $response->getContent(),
                'headers' => ['Content-Type' => $response->headers->get('Content-Type')],
            ], now()->addMinutes(self::TTL_MINUTES));

            Cache::put('page:keys', array_unique([...Cache::get('page:keys', []), $key]), now()->addDay());
        }

        return $response->header('X-Cache', 'miss');
    }

    private function shouldCache(Request $request): bool
    {
        // Anonymous GETs only. A signed-in reader sees their own page, and a
        // draft preview must never be stored and handed to the next visitor.
        return $request->isMethod('GET')
            && ! $request->user()
            && ! $request->has('preview');
    }

    /** Called when content changes. */
    public static function flush(): void
    {
        foreach (Cache::get('page:keys', []) as $key) {
            Cache::forget($key);
        }

        Cache::forget('page:keys');
    }
}
