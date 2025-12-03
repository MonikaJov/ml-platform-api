<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\Dataset;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;

final class TargetColumnIsSuitableForProblemTypeRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = request()->input('type');
        if (! $type) {
            return;
        }

        /** @var Dataset $dataset */
        $dataset = request()->route('dataset');
        $values = $this->extractColumnValues($dataset, $value, $fail);
        if ($values === null) {
            return;
        }

        $unique = collect($values)->unique()->values();

        $this->validateByType($unique, $type, $value, $fail);
    }

    /** @return array<string, string|int>|null */
    private function extractColumnValues(Dataset $dataset, string|int $column, Closure $fail): ?array
    {
        $handle = fopen($dataset->full_path, 'r');

        $header = fgetcsv($handle);
        $targetIndex = array_search($column, $header, true);
        if ($targetIndex === false) {
            fclose($handle);
            $fail('Target column "'.$column.'" does not exist in dataset.');

            return null;
        }
        $values = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (! array_key_exists($targetIndex, $row)) {
                continue;
            }
            $cell = mb_trim((string) $row[$targetIndex]);
            if ($cell !== '') {
                $values[] = $cell;
            }
        }

        fclose($handle);

        return $values;
    }

    private function validateByType(Collection $unique, string $type, string $column, Closure $fail): void
    {
        match ($type) {
            ProblemDetailTypeEnum::REGRESSION->value => $this->validateRegression($unique, $column, $fail),
            ProblemDetailTypeEnum::CLASSIFICATION->value => $this->validateClassification($unique, $column, $fail),
            ProblemDetailTypeEnum::BINARY_CLASSIFICATION->value => $this->validateBinary($unique, $column, $fail),
            ProblemDetailTypeEnum::CLUSTERING->value => $this->validateClustering($unique, $column, $fail),
            default => $fail('Unknown problem type.')
        };
    }

    private function validateRegression(Collection $unique, string $column, Closure $fail): void
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

    private function validateClassification(Collection $unique, string $column, Closure $fail): void
    {
        if ($unique->count() < 2) {
            $fail("Column '{$column}' is not suitable for classification; it needs at least two distinct labels.");
        }
    }

    private function validateBinary(Collection $unique, string $column, Closure $fail): void
    {
        if ($unique->count() !== 2) {
            $fail("Column '{$column}' is not suitable for binary classification; it must have exactly two distinct labels.");
        }
    }

    private function validateClustering(Collection $unique, string $column, Closure $fail): void
    {
        if ($unique->count() > 0) {
            $fail("Column '{$column}' must be empty for clustering problems.");
        }
    }
}
