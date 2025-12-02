<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Http\Requests\User\PatchUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchUser
{
    use AsAction;

    public function handle(PatchUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return UserResource::make($user);
    }
}
