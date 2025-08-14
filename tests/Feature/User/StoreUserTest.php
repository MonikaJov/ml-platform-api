<?php

namespace Tests\Feature;

use App\Models\User;

beforeEach(function () {
    $this->routeName = 'api.auth.register';
});

dataset('store user data', [
    fn () => [
        'username' => 'username',
        'email' => 'user@example.io',
        'password' => 'password',
        'password_confirmation' => 'password',
        'full_name' => 'full name',
        'mobile' => '+555 555 5555',
    ],
]);

it('registers new users', function (array $userData) {
    $response = $this->postJson(route($this->routeName), $userData);

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['user', 'expires_at', 'access_token'])
        ->and($response->json('user'))
        ->toHaveKeys(['id', 'username', 'email', 'full_name', 'mobile', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('users', [
        'username' => $userData['username'],
    ]);
})->with('store user data');

it('cannot create a user with invalid data', function () {
    $response = $this->postJson(route($this->routeName), [
        'username' => 123,
        'email' => 123,
        'password' => 123,
        'password_confirmation' => 1234,
        'full_name' => 123,
        'mobile' => 123,
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username field must be a string.')
        ->and($response->json('errors')['email'])->toContain('The email field must be a string.')
        ->and($response->json('errors')['email'])->toContain('The email field must be a valid email address.')
        ->and($response->json('errors')['password'])->toContain('The password field must be a string.')
        ->and($response->json('errors')['password'])->toContain('The password field must be at least 8 characters.')
        ->and($response->json('errors')['password'])->toContain('The password field confirmation does not match.')
        ->and($response->json('errors')['full_name'])->toContain('The full name field must be a string.')
        ->and($response->json('errors')['mobile'])->toContain('The mobile field must be a string.');

    $this->assertDatabaseCount('users', 0);
});

it('cannot create a user without required parameters', function () {
    $response = $this->postJson(route($this->routeName), []);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username field is required.')
        ->and($response->json('errors')['email'])->toContain('The email field is required.')
        ->and($response->json('errors')['password'])->toContain('The password field is required.')
        ->and($response->json('errors')['full_name'])->toContain('The full name field is required.');

    $this->assertDatabaseCount('users', 0);
});

it('cannot create a user if username and email are not unique', function (array $userData) {
    User::factory()->create([
        'username' => $userData['username'],
        'email' => $userData['email'],
    ]);

    $response = $this->postJson(route($this->routeName), $userData);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username has already been taken.')
        ->and($response->json('errors')['email'])->toContain('The email has already been taken.');
})->with('store user data');
