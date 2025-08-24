<?php

namespace App\Http\Resources\Dataset;

use App\Http\Resources\BaseJsonResource;
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
            'has_null' => $this->has_null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
