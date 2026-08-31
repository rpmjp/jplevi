<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Honours redirects left behind by slug changes.
 *
 * Only runs on a 404, so the lookup costs nothing on the pages people actually
 * reach. The table is cached because it changes rarely and is read on every
 * miss, including the ones bots generate.
 */
class ServeRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 404) {
            return $response;
        }

        $path = '/'.ltrim($request->path(), '/');

        $map = Cache::remember('redirects.map', now()->addHour(),
            fn () => Redirect::pluck('status', 'from')->all());

        if (! array_key_exists($path, $map)) {
            return $response;
        }

        $target = Redirect::where('from', $path)->value('to');

        return redirect($target, $map[$path]);
    }
}
