<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Keep CSP strict in production and only relax where development tooling needs it.
        $isDev = app()->environment('local');
        $scriptUnsafeInline = $isDev ? " 'unsafe-inline'" : '';
        $viteHosts = $isDev
            ? " http://127.0.0.1:5173 http://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5173"
            : '';

        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self'" . $scriptUnsafeInline . $viteHosts . " https://app.sandbox.midtrans.com https://app.midtrans.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
        $csp .= "style-src 'self' 'unsafe-inline'" . $viteHosts . " https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
        $csp .= "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; ";
        $csp .= "img-src 'self' data: blob: https:; ";
        $csp .= "media-src 'self' data: blob: https:; ";
        $csp .= "connect-src 'self'" . $viteHosts . " https://app.sandbox.midtrans.com https://app.midtrans.com wss://ws-mt1.pusher.com wss://sockjs-mt1.pusher.com https://sockjs-mt1.pusher.com; ";
        $csp .= "frame-src 'self' https://app.sandbox.midtrans.com https://app.midtrans.com;";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
