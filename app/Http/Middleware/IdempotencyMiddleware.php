<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST')) {
            return $next($request);
        }

        $key = $request->header('X-Idempotency-Key');

        if (! $key) {
            return $next($request);
        }

        $cacheKey = "idempotency_{$key}";

        if ($cachedResponse = Cache::get($cacheKey)) {
            return response()->json(
                $cachedResponse['body'],
                $cachedResponse['status'],
                ['X-Cache-Idempotency' => 'true']
            );
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            Cache::put($cacheKey, [
                'body' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode(),
            ], now()->addDay());
        }

        return $response;
    }
}
