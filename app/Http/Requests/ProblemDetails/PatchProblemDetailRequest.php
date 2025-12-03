<?php

declare(strict_types=1);

namespace App\Http\Requests\ProblemDetails;

use App\Enums\ProblemDetailTypeEnum;
use App\Helpers\Rules\RequiredWithoutAllHelper;
use App\Rules\TargetColumnIsSuitableForProblemTypeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PatchProblemDetailRequest extends FormRequest
{
    private const array COLUMNS = [
        'type',
        'target_column',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        $requiredWithoutAllHelper = new RequiredWithoutAllHelper(collect(self::COLUMNS));

        return [
            'type' => [
                'nullable',
                'string',
                'required_without_all:'.$requiredWithoutAllHelper->handle('type'),
                Rule::in(ProblemDetailTypeEnum::values()),
            ],
            'target_column' => [
                'nullable',
                'string',
                'required_without_all:'.$requiredWithoutAllHelper->handle('target_column'),
                new TargetColumnIsSuitableForProblemTypeRule(),
            ],
        ];
    }
}
