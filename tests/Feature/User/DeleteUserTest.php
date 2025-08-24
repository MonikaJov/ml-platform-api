<?php

namespace Tests\Feature;

use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->routeName = 'api.users.delete';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->token = JWTAuth::fromUser($this->user);
    $this->withHeader('Authorization', 'Bearer '.$this->token);
});

it('deletes user', function () {
    $response = $this->deleteJson(route($this->routeName, [
        'user' => $this->user->id,
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['message', 'code'])
        ->and($response->json('message'))->toBe('User successfully deleted');

    $this->assertDatabaseMissing('users', [
        'username' => $this->user->username,
    ]);
});

it('cannot delete user that does not exist', function () {
    $response = $this->deleteJson(route($this->routeName, [
        'user' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot delete if user is not authenticated', function () {
    auth()->logout();

    $response = $this->deleteJson(route($this->routeName, [
        'user' => $this->user->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot delete user that is not you', function () {
    $differentUser = User::factory()->create();

    $response = $this->deleteJson(route($this->routeName, [
        'user' => $differentUser->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
