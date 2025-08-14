<?php

namespace App\Http\Controllers;

use App\Actions\Login;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;

class LoginController extends Controller
{
    /**
     * api.auth.login
     */
    public function __invoke(LoginRequest $request): AuthResource
    {
        return Login::run($request);
    }
}
