<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthResource extends BaseJsonResource
{
    private string $accessToken;

    private Carbon $expiresAt;

    private User $user;

    public function __construct($resource, string $accessToken)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
        self::setExpiresAt();
        $this->user = auth()->user();
    }

    public static function fromToken(string $token): self
    {
        return new self(null, $token);
    }

    /** @return array<string, string> */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
            'expires_at' => $this->expiresAt,
            'access_token' => $this->accessToken,
        ];
    }

    private function setExpiresAt(): void
    {
        $this->expiresAt = Carbon::createFromTimestamp(JWTAuth::setToken($this->accessToken)->getPayload()->get('exp'));
    }
}
