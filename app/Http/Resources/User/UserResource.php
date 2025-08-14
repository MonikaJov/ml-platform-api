<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseJsonResource;
use App\Models\User;
use Illuminate\Http\Request;

/** @mixin User */
class UserResource extends BaseJsonResource
{
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
