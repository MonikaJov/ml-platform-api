<?php

namespace App\Actions;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreUser
{
    use AsAction;

    public function handle(StoreUserRequest $request): AuthResource
    {
        User::create(Arr::except($request->validated(), ['password_confirmation']));

        $credentials = $request->only('username', 'password');
        $token = auth()->attempt($credentials);

        return AuthResource::fromToken($token);
    }
}
