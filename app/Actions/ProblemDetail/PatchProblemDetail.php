<?php

declare(strict_types=1);

namespace App\Actions\ProblemDetail;

use App\Http\Requests\ProblemDetails\PatchProblemDetailRequest;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\ProblemDetail;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchProblemDetail
{
    use AsAction;

    public function handle(PatchProblemDetailRequest $request, ProblemDetail $problemDetail): ProblemDetailResource
    {
        $problemDetail->update($request->validated());

        return ProblemDetailResource::make($problemDetail->load('dataset'));
    }
}
