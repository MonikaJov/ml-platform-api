<?php

namespace App\Http\Resources\BestModel;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PredictionResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            $this['target_column'] => $this['predicted_value'],
        ];
    }
}
