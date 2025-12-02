<?php

namespace Tests\Feature;

use App\Models\BestModel;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.datasets.delete';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

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

        $this->problemDetail = ProblemDetail::factory()->create([
            'dataset_id' => $this->dataset->id,
        ]);

        BestModel::factory()->create([
            'problem_detail_id' => $this->problemDetail->id,
            'dataset_id' => $this->dataset->id,
        ]);
    },
]);

it('deletes a dataset with problem detail and best model', function () {
    $this->assertDatabaseHas('datasets', [
        'path' => $this->dataset->path,
    ]);
    $this->assertDatabaseCount('problem_details', 1);
    $this->assertDatabaseCount('best_models', 1);

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
    $this->assertDatabaseCount('problem_details', 0);
    $this->assertDatabaseCount('best_models', 0);

    Storage::disk('datasets')->assertMissing($this->dataset->path);
})->with('dataset');

it('cannot delete item that does not exist', function () {
    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot delete if user is not authenticated', function () {
    auth()->logout();

    $response = $this->deleteJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot delete item that is not created by authenticated user', function () {
    $differentDataset = Dataset::factory()->createQuietly();

    $response = $this->deleteJson(route($this->routeName, [
        'dataset' => $differentDataset->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
