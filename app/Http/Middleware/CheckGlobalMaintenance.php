<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGlobalMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $isGlobalMaintenanceOn = filter_var(
            SiteSetting::getValue('global_maintenance_mode', false),
            FILTER_VALIDATE_BOOLEAN
        );

        if (! $isGlobalMaintenanceOn) {
            return $next($request);
        }

        // Admin bypass — admin tetap bisa akses semua untuk perbaikan
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Bypass admin routes & auth (biar bisa login/turn off)
        if ($request->is('admin/*') || 
            $request->is('anggita-access') || 
            $request->routeIs('login', 'logout', 'auth.google', 'auth.google.callback')) {
            return $next($request);
        }

        // Hindari redirect loop jika sudah di halaman error 503
        // Laravel otomatis melayani 503 melalui folder errors jika dilempar abort(503)
        abort(503, SiteSetting::getValue('global_maintenance_message', 'Website sedang dalam pemeliharaan rutin.'));
    }
}
