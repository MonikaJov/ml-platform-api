<?php

namespace App\Http\Requests\ProblemDetails;

use App\Enums\ProblemDetailTypeEnum;
use App\Rules\TargetColumnIsSuitableForProblemTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProblemDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(ProblemDetailTypeEnum::values()),
            ],
            'target_column' => [
                'required',
                'string',
                new TargetColumnIsSuitableForProblemTypeRule,
            ],
        ];
    }
}
