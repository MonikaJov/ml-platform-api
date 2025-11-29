<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyInternalServiceTokenMiddleware
{
    public function handle(Request $request, Closure $next)
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
