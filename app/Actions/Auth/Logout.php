<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Tymon\JWTAuth\Facades\JWTAuth;

final class Logout
{
    use AsAction;

    public function handle(): void
    {
        $token = JWTAuth::parseToken();

        JWTAuth::invalidate($token);
    }
}
