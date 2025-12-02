<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class DatasetPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Dataset $dataset): bool
    {
        return $user->id === $dataset->user_id;
    }
}
