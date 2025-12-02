<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class VerifyInternalServiceTokenMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $incoming = $request->header(config('app.ml_platform_internal_auth.header'));

        $expected = config('app.ml_platform_internal_auth.token');

        if (! $incoming || ! hash_equals($expected, $incoming)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $request->headers->remove(config('app.ml_platform_internal_auth.header'));

        return $next($request);
    }
}
