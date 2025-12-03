<?php

declare(strict_types=1);

namespace App\Http\Requests\Dataset;

use App\Rules\FileMustNotBeEmptyRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string|FileMustNotBeEmptyRule> */
    public function rules(): array
    {
        return [
            'dataset' => ['required', 'file', 'mimes:csv', new FileMustNotBeEmptyRule()],
            'has_null' => ['sometimes', 'boolean'],
        ];
    }
}
