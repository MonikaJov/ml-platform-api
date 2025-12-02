<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

final class AuthResource extends BaseJsonResource
{
    private string $accessToken;

    private Carbon $expiresAt;

    private User $user;

    public function __construct(mixed $resource, string $accessToken)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
        $this->expiresAt = $this->extractExpiryFromToken($accessToken);
        $this->user = auth()->user();
    }

    public static function fromToken(string $token): self
    {
        return new self(null, $token);
    }

    /** @return array<string, string|UserResource|Carbon> */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
            'expires_at' => $this->expiresAt,
            'access_token' => $this->accessToken,
        ];
    }

    private function extractExpiryFromToken(string $token): Carbon
    {
        return Carbon::createFromTimestamp(JWTAuth::setToken($token)->getPayload()->get('exp'));
    }
}
