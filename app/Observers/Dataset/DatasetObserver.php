<?php

namespace App\Observers\Dataset;

use App\Models\Dataset;
use Illuminate\Support\Facades\Storage;

class DatasetObserver
{
    public function creating(Dataset $dataset): void
    {
        $dataset->user_id = auth()->user()->id;
    }

    public function deleting(Dataset $dataset): void
    {
        Storage::disk('datasets')->delete($dataset->path);
    }
}
