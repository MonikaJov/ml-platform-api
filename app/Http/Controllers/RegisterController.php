<?php

namespace App\Http\Controllers;

use App\Actions\Register;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;

class RegisterController extends Controller
{
    /**
     * api.auth.register
     */
    public function __invoke(RegisterRequest $request): AuthResource
    {
        return Register::run($request);
    }
}
