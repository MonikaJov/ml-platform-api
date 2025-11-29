<?php

namespace App\Http\Resources\BestModel;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Dataset\DatasetResource;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\BestModel;
use Illuminate\Http\Request;

/** @mixin BestModel */
class BestModelResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
            'problem_detail' => ProblemDetailResource::make($this->whenLoaded('problemDetail')),
            'dataset' => DatasetResource::make($this->whenLoaded('dataset')),
            'name' => $this->name,
            'performance' => $this->performance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
