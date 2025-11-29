<?php

namespace App\Http\Requests\BestModel;

use Illuminate\Foundation\Http\FormRequest;

class StoreBestModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
