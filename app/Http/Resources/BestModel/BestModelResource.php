<?php

declare(strict_types=1);

namespace App\Http\Resources\BestModel;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\Dataset\DatasetResource;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Models\BestModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** @mixin BestModel */
final class BestModelResource extends BaseJsonResource
{
    /** @return array<string, string|int|ProblemDetailResource|DatasetResource|Carbon> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
            'problem_detail' => ProblemDetailResource::make($this->whenLoaded('problemDetail')),
            'name' => $this->name,
            'performance' => $this->performance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
