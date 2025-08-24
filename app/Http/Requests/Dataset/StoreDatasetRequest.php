<?php

namespace App\Http\Requests\Dataset;

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
            'dataset' => ['required', 'file', 'mimes:csv'],
            'has_null' => ['required', 'boolean', 'default' => false],
        ];
    }
}
