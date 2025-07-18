<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Tymon\JWTAuth\Facades\JWTAuth;

class Logout
{
    use AsAction;

    public function handle(): void
    {
        $token = JWTAuth::parseToken();

        JWTAuth::invalidate($token);
    }
}
