<?php

namespace Modules\Admin\Tests\Feature\Authentications;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->routeName = 'api.auth.logout';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->token = JWTAuth::fromUser($this->user);
});

it('logs out', function () {
    JWTAuth::shouldReceive('parseToken')
        ->once()
        ->andReturn($this->token);

    JWTAuth::shouldReceive('invalidate')
        ->once()
        ->with($this->token);

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(200)
        ->and($response->json('message'))->toBe('Successfully logged out');
});
