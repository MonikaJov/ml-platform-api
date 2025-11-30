<?php

namespace App\Http\Middleware;

use App\Models\Dataset;
use Closure;
use Illuminate\Http\Request;

class FileMustExistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Dataset $dataset */
        $dataset = $request->route('dataset');

        if (! file_exists($dataset->getFullPath())) {
            return response()->json(['error' => __('The dataset file does not exist.')], 422);
        }

        if (@fopen($dataset->getFullPath(), 'r') === false) {
            return response()->json(['error' => __('The dataset file cannot be opened.')], 422);
        }

        return $next($request);
    }
}
