<?php

namespace App\Http\Requests\User;

use App\Helpers\Rules\RequiredWithoutAllHelper;
use Illuminate\Foundation\Http\FormRequest;

class PatchUserRequest extends FormRequest
{
    private const array COLUMNS = [
        'username',
        'email',
        'password',
        'full_name',
        'mobile',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiredWithoutAllHelper = new RequiredWithoutAllHelper(collect(self::COLUMNS));

        $userId = $this->route()->parameter('user')->id ?? null;

        return [
            'username' => ['nullable', 'string', 'unique:users,username,'.$userId, 'required_without_all:'.$requiredWithoutAllHelper->handle('username'),],
            'email' => ['nullable', 'string', 'email', 'unique:users,email,'.$userId, 'required_without_all:'.$requiredWithoutAllHelper->handle('email'),],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'required_without_all:'.$requiredWithoutAllHelper->handle('password'),],
            'full_name' => ['nullable', 'string', 'required_without_all:'.$requiredWithoutAllHelper->handle('full_name'),],
            'mobile' => ['nullable', 'string', 'required_without_all:'.$requiredWithoutAllHelper->handle('mobile'),],
        ];
    }
}
