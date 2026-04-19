<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AntiSpamHoneypot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika input 'hp_field' terisi, berarti yang mengisinya adalah bot otomatis
        // karena manusia tidak akan melihat input ini (disembunyikan oleh CSS)
        if ($request->filled('hp_field')) {
            \Log::warning('Honeypot triggered: Probable bot detected.', [
                'ip' => $request->ip(),
                'data' => $request->all(),
            ]);

            // Berikan respon sukses palsu atau abaikan agar bot merasa berhasil tapi tidak ada data tersimpan
            return response()->json(['success' => true], 200);
        }

        return $next($request);
    }
}
