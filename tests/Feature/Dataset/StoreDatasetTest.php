<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->routeName = 'api.datasets.store';
    Storage::fake('datasets');
});

dataset('valid dataset files', [
    fn () => [
        'dataset' => UploadedFile::fake()->create('data.csv', 100, 'text/csv'),
        'has_null' => true,
    ],
]);

it('stores a valid dataset', function (array $datasetData) {
    $this->assertDatabaseCount('datasets', 0);

    $response = $this->postJson(route($this->routeName), $datasetData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'has_null', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
        'has_null' => true,
    ]);

    Storage::disk('datasets')->assertExists($response->json('data.path'));
})->with('valid dataset files');

it('stores a dataset without has_null parameter', function (array $datasetData) {
    $this->assertDatabaseCount('datasets', 0);

    $response = $this->postJson(route($this->routeName), [
        'dataset' => $datasetData['dataset'],
    ]);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'has_null', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
        'has_null' => false,
    ]);

    Storage::disk('datasets')->assertExists($response->json('data.path'));
})->with('valid dataset files');

it('cannot store an invalid file type', function () {
    $response = $this->postJson(route($this->routeName), [
        'dataset' => UploadedFile::fake()->create('data.txt', 100, 'text/plain'),
        'has_null' => false,
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field must be a file of type: csv.');

    $this->assertDatabaseCount('datasets', 0);
});

it('cannot store without required parameters', function () {
    $response = $this->postJson(route($this->routeName), []);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field is required.');

    $this->assertDatabaseCount('datasets', 0);
});
