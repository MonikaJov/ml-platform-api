<?php

namespace App\Observers\ProblemDetail;

use App\Models\ProblemDetail;

class ProblemDetailObserver
{
    public function deleting(ProblemDetail $problemDetail): void
    {
        $problemDetail->bestModel?->delete();
    }
}
