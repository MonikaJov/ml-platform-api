<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->routeName = 'api.auth.login';

    $this->user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('password'),
    ]);
});

it('logs in with valid credentials', function () {
    $response = $this->postJson(route($this->routeName), [
        'username' => 'testuser',
        'password' => 'password',
    ]);

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['access_token', 'expires_at', 'user'])
        ->and($response->json('expires_at'))->toBeString()->and(fn ($date) => expect(Carbon::parse($date))->toBeInstanceOf(Carbon::class))
        ->and($response->json('user'))
        ->toHaveKeys(['id', 'username', 'email', 'full_name', 'mobile', 'created_at', 'updated_at']);

});

it('cannot log in with invalid credentials', function () {
    $response = $this->postJson(route($this->routeName), [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    expect($response->status())
        ->toBe(400)
        ->and($response->json('error'))->toBe(__('Invalid input data.'));
});
