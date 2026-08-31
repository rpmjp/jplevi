<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Counts reads without identifying readers.
 *
 * Rows are aggregated by path, referring host and day, so a busy page is one
 * row with a counter rather than thousands of rows carrying a footprint of who
 * was there. Bots and prefetches are skipped so the numbers mean something.
 */
class RecordPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldCount($request, $response)) {
            try {
                $this->record($request);
            } catch (\Throwable $e) {
                // Counting reads is never worth failing a page for. Log it and
                // serve the reader what they came for.
                report($e);
            }
        }

        return $response;
    }

    private function shouldCount(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return false;
        }

        // Browsers prefetching a link have not read anything.
        if ($request->headers->get('Purpose') === 'prefetch' || $request->headers->get('Sec-Purpose')) {
            return false;
        }

        $agent = strtolower((string) $request->userAgent());

        return $agent !== '' && ! preg_match('/bot|crawl|spider|slurp|headless|monitor|preview/i', $agent);
    }

    private function record(Request $request): void
    {
        $path = '/'.trim($request->path(), '/');
        $host = $request->headers->get('referer')
            ? parse_url($request->headers->get('referer'), PHP_URL_HOST)
            : null;

        // Never store our own pages as a referrer: that is navigation, not
        // discovery, and it drowns out where readers actually came from.
        //
        // Empty string rather than null, because both MySQL and SQLite treat
        // NULLs as distinct inside a unique index, so a null referrer would
        // insert a fresh row on every single view instead of incrementing.
        $host = ($host && $host !== $request->getHost()) ? $host : '';

        // Only blog URLs can belong to a post, so everything else avoids the
        // lookup entirely rather than querying on every request to the site.
        $postId = str_starts_with($path, '/blog/')
            ? Post::where('slug', basename($path))->value('id')
            : null;

        $view = PageView::firstOrCreate(
            [
                'path' => $path,
                'referrer_host' => $host,
                'viewed_on' => now()->toDateString(),
            ],
            ['post_id' => $postId, 'views' => 0],
        );

        $view->increment('views');
    }
}
