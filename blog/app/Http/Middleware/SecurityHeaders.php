<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response headers the browser enforces on our behalf.
 *
 * The content security policy is the one that matters: posts contain HTML we
 * wrote, but comments contain text other people wrote, and a policy that
 * refuses inline and foreign script is what keeps a mistake in escaping from
 * becoming someone else's JavaScript running on our domain.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "frame-src https://www.youtube-nocookie.com https://player.vimeo.com",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
        ]);

        foreach ([
            'Content-Security-Policy' => $csp,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
        ] as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }
}
