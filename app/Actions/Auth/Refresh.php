<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Http\Resources\Auth\AuthResource;
use Lorisleiva\Actions\Concerns\AsAction;
use Tymon\JWTAuth\Facades\JWTAuth;

final class Refresh
{
    use AsAction;

    public function handle(string $oldToken): AuthResource
    {
        JWTAuth::setToken($oldToken);

        $token = JWTAuth::refresh($oldToken);

        JWTAuth::setToken($token)->toUser();

        return AuthResource::fromToken($token);
    }
}
