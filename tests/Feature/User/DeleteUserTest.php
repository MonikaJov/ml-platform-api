<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.users.delete';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('deletes a user along with their datasets', function () {
    Storage::fake('datasets');

    $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email'.PHP_EOL.'1,Ana,ana@example.com'.PHP_EOL);
    $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

    $dataset = Dataset::factory()->create([
        'path' => $this->user->id.'/'.$file->name,
        'user_id' => $this->user->id,
    ]);

    Storage::disk('datasets')->assertExists($dataset->path);

    $this->assertDatabaseHas('users', [
        'username' => $this->user->username,
    ]);
    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
    ]);

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

    $this->assertDatabaseMissing('datasets', [
        'user_id' => $this->user->id,
    ]);

    Storage::disk('datasets')->assertMissing($dataset->path);
});

it('cannot delete item that does not exist', function () {
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

it('cannot delete user that is not authenticated user', function () {
    $differentUser = User::factory()->create();

    $response = $this->deleteJson(route($this->routeName, [
        'user' => $differentUser->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
