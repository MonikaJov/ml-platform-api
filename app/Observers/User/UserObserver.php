<?php

namespace App\Observers\User;

use App\Models\User;

class UserObserver
{
    public function deleting(User $user): void
    {
        foreach ($user->datasets as $dataset) {
            $dataset->delete();
        }
    }
}
