<?php

namespace App\Http\Requests\Dataset;

use App\Rules\FileMustNotBeEmptyRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dataset' => ['required', 'file', 'mimes:csv', new FileMustNotBeEmptyRule],
            'has_null' => ['sometimes', 'boolean'],
        ];
    }
}
