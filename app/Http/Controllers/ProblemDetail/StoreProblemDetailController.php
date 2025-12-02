<?php

declare(strict_types=1);

namespace App\Http\Controllers\ProblemDetail;

use App\Actions\ProblemDetail\StoreProblemDetail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemDetails\StoreProblemDetailRequest;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\Dataset;

final class StoreProblemDetailController extends Controller
{
    /**
     * api.dataset.problem-details.store
     */
    public function __invoke(StoreProblemDetailRequest $request, Dataset $dataset): ProblemDetailResource
    {
        return StoreProblemDetail::run($request, $dataset);
    }
}
