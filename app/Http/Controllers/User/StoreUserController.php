<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\StoreUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\Auth\AuthResource;
use Dedoc\Scramble\Attributes\Group;

#[Group('User')]
final class StoreUserController extends Controller
{
    /**
     * api.auth.register
     */
    public function __invoke(StoreUserRequest $request): AuthResource
    {
        return StoreUser::run($request);
    }
}
