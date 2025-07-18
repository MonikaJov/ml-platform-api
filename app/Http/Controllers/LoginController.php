<?php

namespace App\Http\Controllers;

use App\Actions\Login;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;

class LoginController extends Controller
{
    /**
     * api.admin.auth.login
     */
    public function __invoke(LoginRequest $request): AuthResource
    {
        return Login::run($request);
    }
}
