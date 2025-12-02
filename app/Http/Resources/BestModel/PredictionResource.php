<?php

declare(strict_types=1);

namespace App\Http\Resources\BestModel;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

final class PredictionResource extends BaseJsonResource
{
    /** @return array<string, string|int> */
    public function toArray(Request $request): array
    {
        return [
            $this['target_column'] => $this['predicted_value'],
        ];
    }
}
