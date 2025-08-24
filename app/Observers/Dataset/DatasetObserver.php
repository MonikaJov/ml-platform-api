<?php

namespace App\Observers\Dataset;

use App\Models\Dataset;

class DatasetObserver
{
    public function creating(Dataset $dataset): void
    {
        $dataset->user_id = auth()->user()->id;
    }
}
