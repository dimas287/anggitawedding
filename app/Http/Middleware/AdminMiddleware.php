<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminGuard = auth('admin');

        if ($adminGuard->check()) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->isAdmin()) {
            $adminGuard->login(auth()->user());
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return redirect()->route('login')->with('error', 'Akses khusus admin.');
    }
}
