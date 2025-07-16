<?php

namespace App\Http\Controllers;

use App\Actions\StoreUser;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\AuthResource;

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
