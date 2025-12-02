<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\Logout;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulOperationMessageResource;
use Dedoc\Scramble\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Authentication')]
final class LogoutController extends Controller
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
