<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->routeName = 'api.auth.register';
});

dataset('user data', [
    fn () => [
        'username' => 'username',
        'email' => 'user@example.io',
        'password' => 'password',
        'password_confirmation' => 'password',
        'full_name' => 'full name',
        'mobile' => '+555 555 5555',
    ],
]);

it('stores a user', function (array $userData) {
    $response = $this->postJson(route($this->routeName), $userData);

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['user', 'expires_at', 'access_token'])
        ->and($response->json('user'))
        ->toHaveKeys(['id', 'username', 'email', 'full_name', 'mobile', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('users', [
        'username' => $userData['username'],
        'email' => $userData['email'],
        'full_name' => $userData['full_name'],
        'mobile' => $userData['mobile'],
    ]);

    $user = User::query()->where('username', $userData['username'])->first();
    expect(Hash::check('password', $user->password))->toBeTrue();
})->with('user data');

it('cannot store if username and email are not unique', function (array $userData) {
    User::factory()->create([
        'username' => $userData['username'],
        'email' => $userData['email'],
    ]);

    $response = $this->postJson(route($this->routeName), $userData);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username has already been taken.')
        ->and($response->json('errors')['email'])->toContain('The email has already been taken.');
})->with('user data');

it('cannot store with invalid data', function () {
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
});

it('cannot store without required parameters', function () {
    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username field is required.')
        ->and($response->json('errors')['email'])->toContain('The email field is required.')
        ->and($response->json('errors')['password'])->toContain('The password field is required.')
        ->and($response->json('errors')['full_name'])->toContain('The full name field is required.');
});
