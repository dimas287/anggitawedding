<?php

namespace App\Http\Middleware;

use App\Models\AdminActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return tap($next($request), function () use ($request) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return;
            }

            try {
                AdminActivity::create([
                    'user_id' => $request->user()->id,
                    'action' => $this->resolveAction($request),
                    'method' => $request->getMethod(),
                    'route' => optional($request->route())->getName(),
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'payload' => $this->extractPayload($request),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }

    protected function resolveAction(Request $request): string
    {
        if ($name = optional($request->route())->getName()) {
            return $name;
        }

        return strtoupper($request->method()) . ' ' . $request->path();
    }

    protected function extractPayload(Request $request): array
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $this->sanitizePayload($request->query());
        }

        $payload = $request->except([
            '_token',
            '_method',
            'password',
            'password_confirmation',
            'current_password',
            'admin_password',
        ]);

        return $this->sanitizePayload($payload);
    }

    protected function sanitizePayload(array $payload): array
    {
        return Arr::map($payload, function ($value, $key) {
            if ($this->isSensitiveKey((string) $key)) {
                return '[REDACTED]';
            }

            if (is_array($value)) {
                return $this->sanitizePayload($value);
            }

            return $this->stringify($value);
        });
    }

    protected function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower($key);
        foreach (['password', 'token', 'secret', 'key', 'authorization', 'cookie'] as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function stringify($value): string
    {
        if (is_scalar($value) || is_null($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
