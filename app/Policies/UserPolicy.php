<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->id === $targetUser->id;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->id === $targetUser->id;
    }
}
