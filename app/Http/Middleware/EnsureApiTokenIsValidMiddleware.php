<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

final class EnsureApiTokenIsValidMiddleware
{
    protected AuthManager $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, mixed ...$guards): mixed
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            return $this->unauthenticated();
        } catch (TokenInvalidException $e) {
            return $this->invalidToken();
        } catch (TokenExpiredException $e) {
            return $this->expiredToken();
        } catch (JWTException $e) {
            return $this->tokenError($e->getMessage());
        }

        return $next($request);
    }

    /**
     * @param  array<string, int|string>  $guards
     *
     * @throws AuthenticationException
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     * @throws JWTException
     */
    protected function authenticate(Request $request, array $guards): void
    {
        if ($guards === []) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $this->auth->shouldUse($guard);

                return;
            }
        }
        $user = JWTAuth::parseToken()->authenticate();
        if (! $user) {
            throw new AuthenticationException(__('Unauthenticated.'), $guards);
        }

        // Set the authenticated user in the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
    }

    protected function unauthenticated(): JsonResponse
    {
        return response()->json(['error' => __('Unauthenticated.')], 401);
    }

    protected function invalidToken(): JsonResponse
    {
        return response()->json(['error' => __('Invalid token.')], 400);
    }

    protected function expiredToken(): JsonResponse
    {
        return response()->json(['error' => __('Token has expired.')], 401);
    }

    protected function tokenError(string $message): JsonResponse
    {
        return response()->json(['error' => $message], 400);
    }
}
