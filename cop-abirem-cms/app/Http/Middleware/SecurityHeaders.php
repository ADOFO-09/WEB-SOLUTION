<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');
        $csp = $this->buildCsp();
        if ($csp !== null) {
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }

    private function buildCsp(): ?string
    {
        // CSP is only applied in production. In local dev, Vite's HMR server
        // binds to an IPv6 address that browsers reject as an invalid CSP source.
        if (app()->environment('local')) {
            return null;
        }

        return
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com data:; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self';";
    }
}
