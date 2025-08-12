<?php

namespace App\Actions;

use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteUser
{
    use AsAction;

    public function handle(User $user): SuccessfulOperationMessageResource
    {
        $user->delete();

        return SuccessfulOperationMessageResource::make('User successfully deleted');
    }
}
