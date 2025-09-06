<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.users.patch';

    $this->user = User::factory()->create([
        'username' => 'username',
        'email' => 'user@example.io',
        'password' => 'password',
        'full_name' => 'full name',
        'mobile' => '+555 555 5555',
    ]);
    $this->actingAs($this->user);
});

dataset('user data', [
    fn () => [
        'username' => 'updatedusername',
        'email' => 'updateduser@example.io',
        'password' => 'updatedpassword',
        'password_confirmation' => 'updatedpassword',
        'full_name' => 'updated full name',
        'mobile' => '+666 666 6666',
    ],
]);

it('updates a user', function (array $userData) {
    $this->assertDatabaseHas('users', [
        'username' => $this->user->username,
        'email' => $this->user->email,
        'password' => $this->user->password,
        'full_name' => $this->user->full_name,
        'mobile' => $this->user->mobile,
    ]);

    $response = $this->patchJson(route($this->routeName, [
        'user' => $this->user->id,
    ]), $userData);

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['id', 'username', 'email', 'full_name', 'mobile', 'created_at', 'updated_at']);

    $this->assertDatabaseMissing('users', [
        'username' => $this->user->username,
        'email' => $this->user->email,
        'full_name' => $this->user->full_name,
        'mobile' => $this->user->mobile,
    ]);

    $this->assertDatabaseHas('users', [
        'username' => $userData['username'],
        'email' => $userData['email'],
        'full_name' => $userData['full_name'],
        'mobile' => $userData['mobile'],
    ]);

    $user = User::query()->where('username', $userData['username'])->first();
    expect(Hash::check('updatedpassword', $user->password))->toBeTrue();
})->with('user data');

it('cannot patch with invalid data', function () {
    $response = $this->patchJson(route($this->routeName, [
        'user' => $this->user->id,
    ]), [
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

it('cannot patch without all parameters', function () {
    $response = $this->patchJson(route($this->routeName, [
        'user' => $this->user->id,
    ]));

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors'))->toHaveKeys(['username', 'email', 'full_name', 'mobile'])
        ->and($response->json('errors')['username'])->toContain('The username field is required when none of email / password / full name / mobile are present.')
        ->and($response->json('errors')['email'])->toContain('The email field is required when none of username / password / full name / mobile are present.')
        ->and($response->json('errors')['password'])->toContain('The password field is required when none of username / email / full name / mobile are present.')
        ->and($response->json('errors')['full_name'])->toContain('The full name field is required when none of username / email / password / mobile are present.');
});

it('cannot patch if username and email are not unique', function (array $userData) {
    User::factory()->create([
        'username' => $userData['username'],
        'email' => $userData['email'],
    ]);

    $response = $this->patchJson(route($this->routeName, [
        'user' => $this->user->id,
    ]), $userData);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['username'])->toContain('The username has already been taken.')
        ->and($response->json('errors')['email'])->toContain('The email has already been taken.');
})->with('user data');

it('cannot patch item that does not exist', function () {
    $response = $this->patchJson(route($this->routeName, [
        'user' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot patch if user is not authenticated', function () {
    auth()->logout();

    $response = $this->patchJson(route($this->routeName, [
        'user' => $this->user->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot patch user that is not authenticated user', function () {
    $differentUser = User::factory()->create();

    $response = $this->patchJson(route($this->routeName, [
        'user' => $differentUser->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
