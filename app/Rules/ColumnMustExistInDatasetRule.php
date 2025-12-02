<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Dataset;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ColumnMustExistInDatasetRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Dataset $dataset */
        $dataset = request()->route('dataset');

        $columns = explode(',', $dataset->column_names);

        $column = explode('.', $attribute)[1];

        if (! in_array($column, $columns)) {
            $fail('Column "'.$column.'" does not exist in dataset.');
        }
    }
}
