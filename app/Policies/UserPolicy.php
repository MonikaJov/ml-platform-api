<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->id === $targetUser->id;
    }
}
