<?php

namespace App\Http\Controllers\User;

use App\Actions\User\DeleteUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\User;

class DeleteUserController extends Controller
{
    /**
     * api.users.delete
     */
    public function __invoke(User $user): SuccessfulOperationMessageResource
    {
        return DeleteUser::run($user);
    }
}
