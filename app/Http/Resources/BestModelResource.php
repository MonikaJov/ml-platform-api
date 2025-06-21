<?php

namespace App\Http\Resources;

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
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
