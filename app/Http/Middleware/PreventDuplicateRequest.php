<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        $idempotencyKey = (string) (
            $request->header('X-Idempotency-Key')
            ?? $request->header('Idempotency-Key')
            ?? $request->input('_idempotency_key')
            ?? ''
        );

        if ($idempotencyKey === '') {
            return $next($request);
        }

        $routeName = optional($request->route())->getName() ?? $request->path();
        $owner = auth()->check()
            ? ('u:' . auth()->id())
            : ($request->hasSession() ? ('s:' . $request->session()->getId()) : ('ip:' . $request->ip()));
        $fingerprint = sha1($routeName . '|' . $request->method() . '|' . $owner . '|' . $idempotencyKey);

        $processingKey = 'idempotency:processing:' . $fingerprint;
        $doneKey = 'idempotency:done:' . $fingerprint;
        $redirectKey = 'idempotency:redirect:' . $fingerprint;

        if (Cache::has($doneKey)) {
            return $this->duplicateResponse($request, Cache::get($redirectKey));
        }

        if (!Cache::add($processingKey, true, now()->addSeconds(30))) {
            return $this->duplicateResponse($request, Cache::get($redirectKey));
        }

        try {
            $response = $next($request);
        } finally {
            Cache::forget($processingKey);
        }

        if ($response->getStatusCode() < 500) {
            Cache::put($doneKey, true, now()->addMinutes(15));

            if ($response instanceof RedirectResponse) {
                Cache::put($redirectKey, $response->getTargetUrl(), now()->addMinutes(15));
            }
        }

        return $response;
    }

    protected function duplicateResponse(Request $request, ?string $redirectUrl = null): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Permintaan yang sama sudah diproses. Mohon tunggu.',
            ], 409);
        }

        if (!empty($redirectUrl)) {
            return redirect()->to($redirectUrl)
                ->with('info', 'Permintaan yang sama sudah diproses.');
        }

        return back()->with('info', 'Permintaan sedang diproses atau sudah diproses.');
    }
}
