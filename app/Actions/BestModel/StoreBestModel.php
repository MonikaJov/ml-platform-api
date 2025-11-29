<?php

namespace App\Actions\BestModel;

use App\Http\Requests\BestModel\StoreBestModelRequest;
use App\Http\Resources\BestModel\BestModelResource;
use App\Models\BestModel;
use App\Models\ProblemDetail;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreBestModel
{
    use AsAction;

    public function handle(StoreBestModelRequest $request): BestModelResource
    {
        $problemDetail = ProblemDetail::where('task_id', $request->task_id)->first();

        BestModel::where('problem_detail_id', $problemDetail->id)->delete();

        $bestModel = BestModel::create([
            'path' => $request->validated()['model_path'],
            'name' => $request->validated()['model_type'],
            'performance' => json_encode($request->validated()['performance']),
            'problem_detail_id' => $problemDetail->id,
            'dataset_id' => $problemDetail->dataset_id,
        ]);

        return BestModelResource::make($bestModel->load('problemDetail', 'dataset.user'));
    }
}
