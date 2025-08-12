<?php

namespace App\Http\Controllers;

use App\Actions\DeleteUser;
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
