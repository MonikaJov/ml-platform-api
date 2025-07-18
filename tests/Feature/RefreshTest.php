<?php

use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->routeName = 'api.auth.refresh';
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->token = JWTAuth::fromUser($this->user);
});

it('successfully refreshes the token', function () {
    $response = $this->postJson(route($this->routeName , ['refresh_token' => $this->token]));

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['access_token', 'expires_at', 'user'])
        ->and($response->json('expires_at'))->toBeString()->and(fn ($date) => expect(Carbon::parse($date))->toBeInstanceOf(Carbon::class))
        ->and($response->json('user'))
        ->toHaveKeys(['id', 'username', 'email', 'full_name', 'mobile', 'created_at', 'updated_at', 'deleted_at']);

});
