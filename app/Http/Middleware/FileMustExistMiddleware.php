<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Dataset;
use Closure;
use Illuminate\Http\Request;

final class FileMustExistMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var Dataset $dataset */
        $dataset = $request->route('dataset');

        if (! file_exists($dataset->full_path)) {
            return response()->json(['error' => __('The dataset file does not exist.')], 422);
        }

        if (! is_readable($dataset->full_path)) {
            return response()->json(['error' => __('The dataset file cannot be opened.')], 422);
        }

        $handle = fopen($dataset->full_path, 'r');

        if ($handle === false) {
            return response()->json(['error' => __('The dataset file cannot be opened.')], 422);
        }

        $header = fgetcsv($handle);

        fclose($handle);

        if (! $header) {
            return response()->json(['error' => __('Dataset file is empty or unreadable.')], 422);
        }

        return $next($request);
    }
}
