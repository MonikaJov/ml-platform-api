<?php

declare(strict_types=1);

namespace App\Http\Requests\Dataset;

use App\Rules\FileMustNotBeEmptyRule;
use App\Rules\SameColumnsAsOriginalRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpsertDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string|FileMustNotBeEmptyRule|SameColumnsAsOriginalRule> */
    public function rules(): array
    {
        return [
            /**
             * The uploaded file must not be empty.<br>
             * The uploaded file must have the same columns as the original.
             */
            'dataset' => [
                'required',
                'file',
                'mimes:csv',
                new FileMustNotBeEmptyRule(),
                new SameColumnsAsOriginalRule(),
            ],
        ];
    }
}
