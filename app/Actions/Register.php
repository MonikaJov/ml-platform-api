<?php

namespace App\Actions;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

class Register
{
    use AsAction;

    public function handle(RegisterRequest $request): AuthResource
    {
        User::create(Arr::except($request->validated(), ['password_confirmation']));

        $credentials = $request->only('username', 'password');
        $token = auth()->attempt($credentials);

        return AuthResource::fromToken($token);
    }
}
