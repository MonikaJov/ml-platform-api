<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Dataset;
use Closure;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

final class SameColumnsAsOriginalRule implements ValidationRule
{
    public static function docs(): ObjectType
    {
        $object = new ObjectType();
        $object->setDescription('Uploaded file must have the same columns as the original.');

        return $object;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! ($value instanceof UploadedFile)) {
            return;
        }
        /** @var Dataset $dataset */
        $dataset = request()->route('dataset');

        $originalColumns = explode(',', $dataset->column_names);

        $handle = fopen($value->getRealPath(), 'r');

        if ($handle === false) {
            return;
        }
        $uploadedColumns = fgetcsv($handle);
        fclose($handle);

        if ($uploadedColumns === false) {
            return;
        }

        if ($uploadedColumns !== $originalColumns) {
            $fail('The uploaded CSV must have the same columns as the original dataset.');
        }
    }
}
