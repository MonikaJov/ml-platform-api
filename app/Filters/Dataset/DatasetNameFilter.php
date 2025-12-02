<?php

declare(strict_types=1);

namespace App\Filters\Dataset;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

final class DatasetNameFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        return $query->where('path', 'like', "%{$value}%");
    }
}
