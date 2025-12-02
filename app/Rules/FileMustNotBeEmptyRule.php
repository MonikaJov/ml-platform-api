<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

final class FileMustNotBeEmptyRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! ($value instanceof UploadedFile)) {
            return;
        }

        $validRowCount = $this->countValidRows($value);

        if ($validRowCount < 2) {
            $fail('The file needs to have at least two non-empty rows.');
        }
    }

    private function countValidRows(UploadedFile $file): int
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return 0;
        }

        $validRowCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) > 0) {
                $validRowCount++;
            }

            if ($validRowCount >= 2) {
                break;
            }
        }

        fclose($handle);

        return $validRowCount;
    }
}
