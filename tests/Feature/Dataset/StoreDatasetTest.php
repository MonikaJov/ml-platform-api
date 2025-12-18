<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.datasets.store';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('datasets');
});

dataset('dataset data', [
    fn () => [
        'dataset' => UploadedFile::fake()->createWithContent('data.csv', 'id,name,email'.PHP_EOL.'1,Ana,ana@example.com'.PHP_EOL),
    ],
]);

it('stores a dataset', function (array $datasetData) {
    $this->assertDatabaseCount('datasets', 0);

    $response = $this->postJson(route($this->routeName), $datasetData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'name', 'column_names', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
        'column_names' => 'id,name,email',
    ]);

    Storage::disk('datasets')->assertExists($response->json('data.path'));
})->with('dataset data');

it('cannot store with invalid data', function () {
    $response = $this->postJson(route($this->routeName), [
        'dataset' => UploadedFile::fake()->create('data.txt', 100, 'text/plain'),
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field must be a file of type: csv.')
        ->and($response->json('errors')['dataset'])->toContain('The file needs to have at least two non-empty rows.');
});

it('cannot store without required parameters', function () {
    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field is required.');
});

it('cannot store if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);
