<?php

namespace Tests\Feature;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.dataset.problem-details.store';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('datasets');
});

dataset('classification problem detail data', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,1'.PHP_EOL);
        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'column_names' => 'id,name,email,gender',
            'user_id' => $this->user->id,
        ]);

        return [
            'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
            'target_column' => 'gender',
        ];
    },
]);

dataset('regression problem detail data', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,experience,salary'.PHP_EOL.'1,Ana,3,2000'.PHP_EOL);
        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'column_names' => 'id,name,experience,salary',
            'user_id' => $this->user->id,
        ]);

        return [
            'type' => ProblemDetailTypeEnum::REGRESSION->value,
            'target_column' => 'salary',
        ];
    },
]);

it('stores classification problem detail', function (array $problemDetailsData) {
    $this->assertDatabaseCount('problem_details', 0);

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'type', 'target_column', 'dataset', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('problem_details', [
        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
        'target_column' => 'gender',
        'dataset_id' => $this->dataset->id,
    ]);
})->with('classification problem detail data');

it('stores regression problem detail', function (array $problemDetailsData) {
    $this->assertDatabaseCount('problem_details', 0);

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'type', 'target_column', 'dataset', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('problem_details', [
        'type' => ProblemDetailTypeEnum::REGRESSION->value,
        'target_column' => 'salary',
        'dataset_id' => $this->dataset->id,
    ]);
})->with('regression problem detail data');

it('cannot store when target column is missing from dataset', function (array $problemDetailsData) {
    $problemDetailsData['target_column'] = 'something else';

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['target_column'])->toContain('Target column "something else" does not exist in dataset.');
})->with('classification problem detail data');

it('cannot store if dataset already has problem details', function (array $problemDetailsData) {
    ProblemDetail::factory()->create([
        'dataset_id' => $this->dataset->id,
    ]);

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(422)
        ->and($response->json('error'))->toContain('Problem detail already exists for this dataset.');
})->with('classification problem detail data');

// TODO: Enable this test after implementing a way to check if a column is empty
// it('cannot store if target column is empty', function () {
//    $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,'.PHP_EOL);
//    $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');
//
//    $dataset = Dataset::factory()->create([
//        'path' => $this->user->id.'/'.$file->name,
//        'user_id' => $this->user->id,
//    ]);
//
//    $response = $this->postJson(route($this->routeName, [
//        'dataset' => $dataset->id
//    ]), [
//        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
//        'target_column' => 'gender',
//        'dataset_id' => $dataset->id,
//    ]);
//
//    expect($response->status())->toBe(422)
//        ->and($response->json('errors')['dataset_id'])->toContain('Target column is empty.');
// });

// TODO: Enable tests after implementing a way to check if a column is suitable for regression/classification
// it('cannot store if target column is not suitable for regression', function (array $problemDetailsData) {
//    $problemDetailsData['type'] = ProblemDetailTypeEnum::REGRESSION->value;
//
//    $response = $this->postJson(route($this->routeName, [
//        'dataset' => $this->dataset->id
//    ]), $problemDetailsData);
//
//    expect($response->status())->toBe(422)
//        ->and($response->json('errors')['type'])->toContain('Target column is not suitable for regression.');
// })->with('classification problem detail data');
//
// it('cannot store if target column is not suitable for classification', function (array $problemDetailsData) {
//    $problemDetailsData['type'] = ProblemDetailTypeEnum::CLASSIFICATION->value;
//
//    $response = $this->postJson(route($this->routeName, [
//        'dataset' => $this->dataset->id
//    ]), $problemDetailsData);
//
//    expect($response->status())->toBe(422)
//        ->and($response->json('errors')['type'])->toContain('Target column is not suitable for classification.');
// })->with('regression problem detail data');

it('cannot store without required parameters', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
    ]));

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['type'])->toContain('The type field is required.')
        ->and($response->json('errors')['target_column'])->toContain('The target column field is required.');
});

it('cannot store if dataset does not exist', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot store with invalid data', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
    ]), [
        'type' => 123,
        'target_column' => 123,
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['type'])->toContain('The type field must be a string.')
        ->and($response->json('errors')['target_column'])->toContain('The target column field must be a string.');
});

it('cannot store if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot store if the dataset in not created by authenticated user', function () {
    $differentDataset = Dataset::factory()->createQuietly();

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $differentDataset->id,
    ]), [
        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
        'target_column' => 'gender',
    ]);

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});
