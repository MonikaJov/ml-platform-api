<?php

declare(strict_types=1);

namespace App\Http\Requests\BestModel;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBestModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'model_path' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            'performance' => ['required', 'array'],
            'task_id' => ['required', 'string', 'exists:problem_details,task_id'],
        ];
    }
}
