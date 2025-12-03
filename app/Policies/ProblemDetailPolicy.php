<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class ProblemDetailPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Dataset $dataset): bool
    {
        return $user->id === $dataset->user_id;
    }

    public function update(User $user, Dataset $dataset): bool
    {
        return $user->id === $dataset->user_id;
    }
}
