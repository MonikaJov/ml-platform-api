<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\Logout;
use App\Http\Resources\SuccessfulOperationMessageResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * api.auth.logout
     */
    public function __invoke(): SuccessfulOperationMessageResource
    {
        Logout::run();

        return SuccessfulOperationMessageResource::make([
            'message' => 'Successfully logged out',
            'code' => Response::HTTP_OK,
        ]);
    }
}
