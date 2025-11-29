<?php

namespace App\Http\Middleware;

use App\Models\Dataset;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileMustExistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Dataset $dataset */
        $dataset = $request->route('dataset');

        $fullPath = Storage::disk('datasets')->path($dataset->path);

        if (! file_exists($fullPath)) {
            return response()->json(['error' => __('The dataset file does not exist.')], 422);
        }

        if (@fopen($fullPath, 'r') === false) {
            return response()->json(['error' => __('The dataset file cannot be opened.')], 422);
        }

        return $next($request);
    }
}
