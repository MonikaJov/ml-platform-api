<?php

declare(strict_types=1);

namespace App\Http\Requests\Dataset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IndexDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'filter.id' => ['sometimes', 'integer'],
            'filter.name' => ['sometimes', 'string'],
            'sort' => ['sometimes', 'array'],
            'sort.*' => ['sometimes', 'string', Rule::in(['id', 'created_at', 'updated_at'])],
        ];
    }
}
