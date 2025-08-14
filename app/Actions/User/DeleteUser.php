<?php

namespace App\Actions\User;

use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class DeleteUser
{
    use AsAction;

    public function handle(User $user): SuccessfulOperationMessageResource
    {
        $user->delete();

        return SuccessfulOperationMessageResource::make([
            'message' => 'User successfully deleted',
            'code' => Response::HTTP_OK,
        ]);
    }
}
