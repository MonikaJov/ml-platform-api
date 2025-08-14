<?php

namespace App\Actions\User;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreUser
{
    use AsAction;

    public function handle(StoreUserRequest $request): AuthResource
    {
        User::create($request->validated());

        $credentials = $request->only('username', 'password');
        $token = auth()->attempt($credentials);

        return AuthResource::fromToken($token);
    }
}
