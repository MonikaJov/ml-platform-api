<?php

namespace App\Http\Resources;

use App\Models\ProblemDetail;
use Illuminate\Http\Request;

/** @mixin ProblemDetail */
class ProblemDetailResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'target_column' => $this->target_column,
            'dataset' => DatasetResource::make($this->whenLoaded('dataset')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
