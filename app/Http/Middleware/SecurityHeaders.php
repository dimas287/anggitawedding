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

        // Content Security Policy (Hardened but Development-Friendly)
        // Content Security Policy (Hardened but Development-Friendly)
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://127.0.0.1:5173 http://localhost:5173 https://app.sandbox.midtrans.com https://app.midtrans.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
        $csp .= "style-src 'self' 'unsafe-inline' http://127.0.0.1:5173 http://localhost:5173 https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; ";
        $csp .= "font-src 'self' data: http://127.0.0.1:8000 http://localhost:8000 http://127.0.0.1:5173 http://localhost:5173 https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; ";
        $csp .= "img-src 'self' data: http://127.0.0.1:8000 http://localhost:8000 http://127.0.0.1:5173 http://localhost:5173 https://app.sandbox.midtrans.com https://app.midtrans.com *; ";
        $csp .= "connect-src 'self' ws://127.0.0.1:5173 ws://localhost:5173 http://127.0.0.1:5173 http://localhost:5173 http://127.0.0.1:8000 http://localhost:8000 https://app.sandbox.midtrans.com https://app.midtrans.com;";;
        $csp .= " frame-src 'self' https://app.sandbox.midtrans.com https://app.midtrans.com;";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
