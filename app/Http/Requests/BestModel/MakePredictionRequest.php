<?php

declare(strict_types=1);

namespace App\Http\Requests\BestModel;

use App\Rules\ColumnMustExistInDatasetRule;
use Illuminate\Foundation\Http\FormRequest;

final class MakePredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*' => ['required', new ColumnMustExistInDatasetRule()],
        ];
    }
}
