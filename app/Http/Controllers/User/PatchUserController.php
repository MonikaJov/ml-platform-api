<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\User\PatchUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\PatchUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;

final class PatchUserController extends Controller
{
    /**
     * api.users.patch
     */
    public function __invoke(PatchUserRequest $request, User $user): UserResource
    {
        return PatchUser::run($request, $user);
    }
}
