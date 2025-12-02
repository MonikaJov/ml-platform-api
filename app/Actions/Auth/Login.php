<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Exceptions\Auth\FailedAuth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Lorisleiva\Actions\Concerns\AsAction;

final class Login
{
    use AsAction;

    /** @throws FailedAuth */
    public function handle(LoginRequest $request): AuthResource
    {
        $credentials = $request->only('username', 'password');
        $token = auth()->attempt($credentials);

        if (! $token) {
            throw new FailedAuth(__('Invalid input data.'));
        }

        return AuthResource::fromToken((string) $token);
    }
}
