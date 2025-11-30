<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecordsMustBeRelatedMiddleware
{
    public function handle(Request $request, Closure $next, string $parentParam, string $childParam, ?string $foreignKey = null): mixed
    {
        /** @var Model|null $parent */
        $parent = $request->route($parentParam);

        /** @var Model|null $child */
        $child = $request->route($childParam);

        $parentParam = Str::snake(class_basename($parent));
        $foreignKey ??= Str::snake($parentParam).'_id';

        if (data_get($child, $foreignKey) !== $parent->getKey()) {
            return response()->json(['error' => __(':child does not belong to the given :parent.', [
                'child' => __(class_basename($child)),
                'parent' => __(class_basename($parent)),
            ])], 422);
        }

        return $next($request);
    }
}
