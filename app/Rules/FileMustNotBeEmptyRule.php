<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class FileMustNotBeEmptyRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! ($value instanceof UploadedFile)) {
            return;
        }

        $handle = fopen($value->getRealPath(), 'r');

        if (! $handle) {
            $fail('The file could not be opened.');

            return;
        }

        $validRowCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (! empty(array_filter($row))) {
                $validRowCount++;
            }

            if ($validRowCount >= 2) {
                break;
            }
        }

        fclose($handle);

        if ($validRowCount < 2) {
            $fail('The file needs to have at least two non-empty rows.');
        }
    }
}
