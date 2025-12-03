<?php

declare(strict_types=1);

namespace App\Http\Controllers\ProblemDetail;

use App\Actions\ProblemDetail\PatchProblemDetail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemDetails\PatchProblemDetailRequest;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Dedoc\Scramble\Attributes\Group;

#[Group('Problem Detail')]
final class PatchProblemDetailController extends Controller
{
    /**
     * api.dataset.problem-details.patch
     */
    public function __invoke(PatchProblemDetailRequest $request, Dataset $dataset, ProblemDetail $problemDetail): ProblemDetailResource
    {
        return PatchProblemDetail::run($request, $problemDetail);
    }
}
