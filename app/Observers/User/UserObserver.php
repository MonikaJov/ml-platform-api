<?php

declare(strict_types=1);

namespace App\Observers\User;

use App\Models\User;

final class UserObserver
{
    public function deleting(User $user): void
    {
        foreach ($user->datasets as $dataset) {
            $dataset->delete();
        }
    }
}
