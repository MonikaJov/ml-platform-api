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
    $this->routeName = 'api.dataset.problem-details.patch';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('datasets');
});

dataset('problem detail data', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv',
            'id,name,email,gender,salary'.PHP_EOL
            .'1,Ana,ana@example,f,2000'.PHP_EOL
            .'2,Bob,bob@example,m,1500'.PHP_EOL
            .'3,Cara,cara@example,f,1600'.PHP_EOL
        );
        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'column_names' => 'id,name,email,gender,salary',
            'user_id' => $this->user->id,
        ]);

        $this->problemDetail = ProblemDetail::factory()->create([
            'dataset_id' => $this->dataset->id,
            'type' => ProblemDetailTypeEnum::CLASSIFICATION,
            'target_column' => 'gender',
        ]);

        return [
            'type' => ProblemDetailTypeEnum::REGRESSION->value,
            'target_column' => 'salary',
        ];
    },
]);

it('updates classification problem detail', function (array $problemDetailsData) {
    $this->assertDatabaseHas('problem_details', [
        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
        'target_column' => 'gender',
        'dataset_id' => $this->dataset->id,
    ]);

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'type', 'target_column', 'dataset', 'created_at', 'updated_at']);

    $this->assertDatabaseHas('problem_details', [
        'type' => ProblemDetailTypeEnum::REGRESSION->value,
        'target_column' => 'salary',
        'dataset_id' => $this->dataset->id,
    ]);
})->with('problem detail data');

it('cannot update when target column is missing from dataset', function (array $problemDetailsData) {
    $problemDetailsData['target_column'] = 'something else';

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['target_column'])->toContain('Target column "something else" does not exist in dataset.');
})->with('problem detail data');

it('cannot update if target column is empty', function () {
    $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,'.PHP_EOL);
    $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

    $dataset = Dataset::factory()->create([
        'path' => $this->user->id.'/'.$file->name,
        'user_id' => $this->user->id,
    ]);

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $dataset->id,
        'problem_detail' => ProblemDetail::factory()->create(['dataset_id' => $dataset->id])->id,
    ]), [
        'type' => ProblemDetailTypeEnum::REGRESSION->value,
        'target_column' => 'gender',
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['target_column'])->toContain('Column \'gender\' is not suitable for regression; it needs more than one value.');
});

it('cannot update if problem detail does not belong to dataset', function () {
    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => ProblemDetail::factory()->create()->id,
    ]), [
        'type' => ProblemDetailTypeEnum::REGRESSION->value,
        'target_column' => 'gender',
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('error'))->toContain('ProblemDetail does not belong to the given Dataset.');
})->with('problem detail data');

it('cannot update if target column is not suitable for problem type', function (array $problemDetailsData) {
    $problemDetailsData['target_column'] = 'gender';

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]), $problemDetailsData);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['target_column'])->toContain('Column \'gender\' is not suitable for regression; it contains non-numeric values.');
})->with('problem detail data');

it('cannot update without required parameters', function () {
    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]));

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['type'])->toContain('The type field is required when none of target column are present.')
        ->and($response->json('errors')['target_column'])->toContain('The target column field is required when none of type are present.');
})->with('problem detail data');

it('cannot update if dataset or problem detail does not exist', function () {
    $response = $this->patchJson(route($this->routeName, [
        'dataset' => 9999,
        'problem_detail' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot update with invalid data', function () {
    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]), [
        'type' => 123,
        'target_column' => 123,
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['type'])->toContain('The type field must be a string.')
        ->and($response->json('errors')['target_column'])->toContain('The target column field must be a string.');
})->with('problem detail data');

it('cannot update if user is not authenticated', function () {
    auth()->logout();

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
        'problem_detail' => ProblemDetail::factory()->create()->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot update if the dataset in not created by authenticated user', function () {
    $this->dataset->update(['user_id' => User::factory()->create()->id]);

    $response = $this->patchJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]), [
        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
        'target_column' => 'gender',
    ]);

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
})->with('problem detail data');

it('cannot update if dataset is unreadable', function () {
    $response = $this->patchJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
        'problem_detail' => ProblemDetail::factory()->create()->id,
    ]), [
        'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
        'target_column' => 'gender',
    ]);

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('Dataset file is empty or unreadable.');
});
