<?php

namespace App\Rules;

use App\Models\Dataset;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DatasetMustContainTargetColumnRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Dataset $dataset */
        $dataset = request()->route('dataset');

        $columns = explode(',', $dataset->column_names);

        if (! in_array($value, $columns)) {
            $fail('Target column "'.$value.'" does not exist in dataset.');
        }
    }
}
