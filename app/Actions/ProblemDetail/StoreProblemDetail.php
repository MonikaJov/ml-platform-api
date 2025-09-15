<?php

namespace App\Actions\ProblemDetail;

use App\Http\Requests\ProblemDetails\StoreProblemDetailRequest;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreProblemDetail
{
    use AsAction;

    public function handle(StoreProblemDetailRequest $request, Dataset $dataset): ProblemDetailResource
    {
        $problemDetail = ProblemDetail::create([
            'dataset_id' => $dataset->id,
            ...$request->validated(),
        ]);

        return ProblemDetailResource::make($problemDetail->load('dataset'));
    }
}
