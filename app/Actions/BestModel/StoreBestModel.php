<?php

declare(strict_types=1);

namespace App\Actions\BestModel;

use App\Http\Requests\BestModel\StoreBestModelRequest;
use App\Http\Resources\BestModel\BestModelResource;
use App\Models\BestModel;
use App\Models\ProblemDetail;
use Lorisleiva\Actions\Concerns\AsAction;

final class StoreBestModel
{
    use AsAction;

    public function handle(StoreBestModelRequest $request): BestModelResource
    {
        $problemDetail = ProblemDetail::where('task_id', $request->task_id)->first();

        $bestModel = BestModel::updateOrCreate(
            [
                'problem_detail_id' => $problemDetail->id,
            ],
            [
                'path' => $request->validated()['model_path'],
                'name' => $request->validated()['model_type'],
                'performance' => json_encode($request->validated()['performance']),
            ]
        );

        return BestModelResource::make($bestModel->load('problemDetail.dataset.user'));
    }
}
