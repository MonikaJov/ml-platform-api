<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Dataset;
use App\Models\ProblemDetail;
use Closure;
use Illuminate\Http\Request;

final class UniquePerModelMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var Dataset $dataset */
        $dataset = $request->route('dataset');

        if (ProblemDetail::where('dataset_id', $dataset->id)->exists()) {
            return response()->json(['error' => __('Problem detail already exists for this dataset.')], 422);
        }

        return $next($request);
    }
}
