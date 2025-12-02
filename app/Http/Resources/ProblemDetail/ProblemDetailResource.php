<?php

declare(strict_types=1);

namespace App\Http\Resources\ProblemDetail;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\BestModel\BestModelResource;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\ProblemDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** @mixin ProblemDetail */
final class ProblemDetailResource extends BaseJsonResource
{
    /** @return array<string, int|string|DatasetResource|BestModelResource|Carbon> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'target_column' => $this->target_column,
            'dataset' => DatasetResource::make($this->whenLoaded('dataset')),
            'best_model' => BestModelResource::make($this->whenLoaded('bestModel')),
            'task_id' => $this->task_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
