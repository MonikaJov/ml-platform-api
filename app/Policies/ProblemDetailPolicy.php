<?php

namespace App\Policies;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProblemDetailPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Dataset $dataset): bool
    {
        return $user->id === $dataset->user_id;
    }
}
