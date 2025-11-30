<?php

namespace App\Http\Resources\Dataset;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Http\Resources\User\UserResource;
use App\Models\Dataset;
use Illuminate\Http\Request;

/** @mixin Dataset */
class DatasetResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'problem_details' => ProblemDetailResource::make($this->whenLoaded('problemDetail')),
            'name' => $this->name,
            'column_names' => $this->column_names,
            'has_null' => $this->has_null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
