<?php

namespace App\Actions;

use App\Exceptions\Auth\FailedAuthException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Lorisleiva\Actions\Concerns\AsAction;

class Login
{
    use AsAction;

    public function handle(LoginRequest $request): AuthResource
    {
        $credentials = $request->only('username', 'password');
        $token = auth()->attempt($credentials);

        if (! $token) {
            throw new FailedAuthException(__('Invalid input data.'));
        }

        return AuthResource::fromToken($token);
    }
}
