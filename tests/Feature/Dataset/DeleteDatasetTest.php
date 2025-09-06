<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->routeName = 'api.datasets.delete';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->token = JWTAuth::fromUser($this->user);
    $this->withHeader('Authorization', 'Bearer '.$this->token);

    Storage::fake('datasets');
});

dataset('dataset', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email'.PHP_EOL.'1,Ana,ana@example.com'.PHP_EOL);

        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'user_id' => $this->user->id,
        ]);
    },
]);

it('deletes dataset', function () {
    $this->assertDatabaseHas('datasets', [
        'path' => $this->dataset->path,
    ]);

    Storage::disk('datasets')->assertExists($this->dataset->path);

    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json())
        ->toHaveKeys(['message', 'code'])
        ->and($response->json('message'))->toBe('Dataset successfully deleted');

    $this->assertDatabaseMissing('datasets', [
        'path' => $this->dataset->path,
    ]);

    Storage::disk('datasets')->assertMissing($this->dataset->path);
})->with('dataset');

it('cannot delete dataset that does not exist', function () {
    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot delete if user is not authenticated', function () {
    auth()->logout();

    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->with('dataset')->throws(JWTException::class);

it('cannot delete dataset that is not created by authenticated user', function () {
    $differentDataset = Dataset::factory()->createQuietly();

    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => $differentDataset->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
