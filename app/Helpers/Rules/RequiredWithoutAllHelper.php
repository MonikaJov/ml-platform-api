<?php

declare(strict_types=1);

namespace App\Helpers\Rules;

use Illuminate\Support\Collection;

final class RequiredWithoutAllHelper
{
    protected Collection $columns;

    public function __construct(Collection $columns)
    {
        $this->columns = $columns;
    }

    public function handle(string $columnName): string
    {
        $columns = $this->columns->reject(function ($column) use ($columnName) {
            return $column === $columnName;
        })->values()->all();

        return implode(',', $columns);
    }
}
