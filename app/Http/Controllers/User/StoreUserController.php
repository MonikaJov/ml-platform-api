<?php

namespace App\Http\Controllers;

use App\Actions\StoreUser;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\Auth\AuthResource;

class StoreUserController extends Controller
{
    /**
     * api.auth.register
     */
    public function __invoke(StoreUserRequest $request): AuthResource
    {
        return StoreUser::run($request);
    }
}
