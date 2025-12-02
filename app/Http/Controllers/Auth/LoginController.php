<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\Login;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;

final class LoginController extends Controller
{
    /**
     * api.auth.login
     */
    public function __invoke(LoginRequest $request): AuthResource
    {
        return Login::run($request);
    }
}
