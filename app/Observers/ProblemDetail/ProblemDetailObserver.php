<?php

declare(strict_types=1);

namespace App\Observers\ProblemDetail;

use App\Models\ProblemDetail;

final class ProblemDetailObserver
{
    public function deleting(ProblemDetail $problemDetail): void
    {
        $problemDetail->bestModel?->delete();
    }
}
