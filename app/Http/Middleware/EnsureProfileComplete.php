<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        $isIncomplete = empty($user->phone) || empty($user->address);

        if (!$isIncomplete) {
            return $next($request);
        }

        $allowedRoutes = [
            'user.profile.complete',
            'user.profile.complete.store',
            'logout',
        ];

        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lengkapi profil Anda sebelum melanjutkan.',
            ], 409);
        }

        return redirect()->route('user.profile.complete')
            ->with('info', 'Lengkapi data kontak Anda sebelum melanjutkan.');
    }
}
