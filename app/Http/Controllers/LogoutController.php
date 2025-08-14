<?php

namespace App\Http\Controllers;

use App\Actions\Logout;
use App\Http\Resources\SuccessfulOperationMessageResource;
use Symfony\Component\HttpFoundation\Response;

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
