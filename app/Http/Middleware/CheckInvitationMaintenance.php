<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInvitationMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceOn = filter_var(
            SiteSetting::getValue('invitation_maintenance_mode', false),
            FILTER_VALIDATE_BOOLEAN
        );

        if (! $isMaintenanceOn) {
            return $next($request);
        }

        // Admin bypass — admin tetap bisa akses semua
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Hindari redirect loop jika sudah di halaman maintenance
        if ($request->routeIs('invitation.maintenance')) {
            return $next($request);
        }

        $message = SiteSetting::getValue(
            'invitation_maintenance_message',
            'Fitur undangan digital sedang dalam perbaikan dan akan segera kembali. Terima kasih atas kesabarannya!'
        );

        // API request atau AJAX → return JSON 503
        if ($request->is('api/*') || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'maintenance' => true,
                'message'     => $message,
            ], 503);
        }

        // Web request → redirect ke halaman maintenance
        return redirect()->route('invitation.maintenance')
            ->with('maintenance_message', $message);
    }
}
