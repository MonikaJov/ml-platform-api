<?php

declare(strict_types=1);

namespace App\Http\Resources\Dataset;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\ProblemDetail\ProblemDetailResource;
use App\Http\Resources\User\UserResource;
use App\Models\Dataset;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** @mixin Dataset */
final class DatasetResource extends BaseJsonResource
{
    /** @return array<string, int|string|UserResource|ProblemDetailResource|Carbon> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'problem_details' => ProblemDetailResource::make($this->whenLoaded('problemDetail')),
            'name' => $this->name,
            'column_names' => $this->column_names,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
