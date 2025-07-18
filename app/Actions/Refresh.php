<?php

namespace App\Actions;

use App\Http\Resources\AuthResource;
use Lorisleiva\Actions\Concerns\AsAction;
use Tymon\JWTAuth\Facades\JWTAuth;

class Refresh
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
