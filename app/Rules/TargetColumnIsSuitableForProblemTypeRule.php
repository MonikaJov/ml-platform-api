<?php

namespace App\Rules;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\Dataset;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TargetColumnIsSuitableForProblemTypeRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = request()->input('type');

        if (! $type) {
            return;
        }
        /** @var Dataset $dataset */
        $dataset = request()->route('dataset');

        $handle = @fopen($dataset->full_path, 'r');

        $header = fgetcsv($handle);
        $targetIndex = array_search($value, $header, true);

        if ($targetIndex === false) {
            fclose($handle);
            $fail('Target column "'.$value.'" does not exist in dataset.');

            return;
        }

        $values = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (! array_key_exists($targetIndex, $row)) {
                continue;
            }

            $cell = trim((string) $row[$targetIndex]);

            if ($cell !== '') {
                $values[] = $cell;
            }
        }

        fclose($handle);

        $unique = collect($values)->unique()->values();

        match ($type) {
            ProblemDetailTypeEnum::REGRESSION->value => $this->validateRegression($unique, $value, $fail),
            ProblemDetailTypeEnum::CLASSIFICATION->value => $this->validateClassification($unique, $value, $fail),
            ProblemDetailTypeEnum::BINARY_CLASSIFICATION->value => $this->validateBinary($unique, $value, $fail),
            ProblemDetailTypeEnum::CLUSTERING->value => $this->validateClustering($unique, $value, $fail),
            default => $fail('Unknown problem type.')
        };
    }

    private function validateRegression($unique, $column, Closure $fail): void
    {
        foreach ($unique as $val) {
            if (! is_numeric($val)) {
                $fail("Column '{$column}' is not suitable for regression; it contains non-numeric values.");
                break;
            }
        }

        if ($unique->count() < 2) {
            $fail("Column '{$column}' is not suitable for regression; it needs more than one value.");
        }
    }

    private function validateClassification($unique, $column, Closure $fail): void
    {
        if ($unique->count() < 2) {
            $fail("Column '{$column}' is not suitable for classification; it needs at least two distinct labels.");
        }
    }

    private function validateBinary($unique, $column, Closure $fail): void
    {
        if ($unique->count() !== 2) {
            $fail("Column '{$column}' is not suitable for binary classification; it must have exactly two distinct labels.");
        }
    }

    private function validateClustering($unique, $column, Closure $fail): void
    {
        if ($unique->count() > 0) {
            $fail("Column '{$column}' must be empty for clustering problems.");
        }
    }
}
