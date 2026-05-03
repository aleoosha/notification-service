<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST')) {
            return $next($request);
        }

        /** @var string|null $key */
        $key = $request->header('X-Idempotency-Key');

        if (! $key) {
            return $next($request);
        }

        $cacheKey = "idempotency_{$key}";

        /** @var array{body: mixed, status: int}|null $cachedResponse */
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse !== null) {
            return response()->json(
                $cachedResponse['body'],
                $cachedResponse['status'],
                ['X-Cache-Idempotency' => 'true']
            );
        }

        /** @var \Illuminate\Http\Response|JsonResponse $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $content = $response->getContent();

            Cache::put($cacheKey, [
                'body' => json_decode($content ?: '{}', true),
                'status' => $response->getStatusCode(),
            ], now()->addDay());
        }

        return $response;
    }
}
