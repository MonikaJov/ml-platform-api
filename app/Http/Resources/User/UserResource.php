<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\BaseJsonResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** @mixin User */
final class UserResource extends BaseJsonResource
{
    /** @return array<string, int|string|Carbon> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'mobile' => $this->mobile,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
