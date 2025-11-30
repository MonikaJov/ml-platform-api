<?php

namespace App\Http\Requests\BestModel;

use App\Rules\ColumnMustExistInDatasetRule;
use Illuminate\Foundation\Http\FormRequest;

class MakePredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*' => ['required', new ColumnMustExistInDatasetRule],
        ];
    }
}
