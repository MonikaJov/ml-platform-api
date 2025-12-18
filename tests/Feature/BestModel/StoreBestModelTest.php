<?php

namespace Tests\Feature;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\BestModel;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

beforeEach(function () {
    $this->routeName = 'api.best-models.store';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->token = config('app.ml_platform_internal_auth.token');

    $this->withHeader(config('app.ml_platform_internal_auth.header'), $this->token);

    Storage::fake('datasets');
});

dataset('dataset', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv',
            'id,name,gender'.PHP_EOL
            .'1,Ana,f'.PHP_EOL
            .'2,Bob,m'.PHP_EOL
            .'3,Cara,f'.PHP_EOL
        );
        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'user_id' => $this->user->id,
        ]);

        $this->problemDetail = ProblemDetail::factory()->create([
            'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
            'target_column' => 'gender',
            'dataset_id' => $this->dataset->id,
            'task_id' => 'some-task-id',
        ]);

        return [
            'model_type' => 'H2OXGBoostEstimator',
            'model_path' => 'XGBoost_1_AutoML_1_20251125_183431',
            'performance' => [
                'r2' => 0.7614648793998325,
                'rmse' => 0.8105723079215637,
                'mae' => 0.7045880035427567,
            ],
            'task_id' => 'some-task-id',
        ];
    },
]);

it('stores best model', function (array $payload) {
    $response = $this->postJson(route($this->routeName), $payload);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys([
            'id',
            'path',
            'name',
            'performance',
            'problem_detail',
            'created_at',
            'updated_at',
        ])
        ->and($response->json('problem_detail'))->toHaveKeys([
            'id',
            'type',
            'target_column',
            'task_id',
            'created_at',
            'updated_at',
        ])
        ->and($response->json('problem_detail')['dataset'])->toHaveKeys([
            'id',
            'user',
            'created_at',
            'updated_at',
        ]);

    $this->assertDatabaseHas('best_models', [
        'problem_detail_id' => $this->problemDetail->id,
        'path' => $payload['model_path'],
        'name' => $payload['model_type'],
        'performance' => json_encode($payload['performance']),
    ]);
})->with('dataset');

it('updates best model with the same problem detail', function (array $payload) {
    BestModel::factory()->create([
        'problem_detail_id' => $this->problemDetail->id,
        'path' => 'Old_Model_Path',
        'name' => 'Old_Model_Type',
        'performance' => json_encode(['r2' => 0.5]),
    ]);

    $response = $this->postJson(route($this->routeName), $payload);

    expect($response->status())->toBe(200);

    $this->assertDatabaseMissing('best_models', [
        'problem_detail_id' => $this->problemDetail->id,
        'dataset_id' => $this->dataset->id,
        'path' => 'Old_Model_Path',
        'name' => 'Old_Model_Type',
        'performance' => json_encode(['r2' => 0.5]),
    ]);

    $this->assertDatabaseHas('best_models', [
        'problem_detail_id' => $this->problemDetail->id,
        'path' => $payload['model_path'],
        'name' => $payload['model_type'],
        'performance' => json_encode($payload['performance']),
    ]);
})->with('dataset');

it('cannot store best model with invalid token', function (array $payload) {
    $this->withHeader(config('app.ml_platform_internal_auth.header'), 'invalid-token');

    $response = $this->postJson(route($this->routeName), $payload);

    expect($response->status())->toBe(401)
        ->and($response->json('error'))->toBe('Unauthenticated.');
})->with('dataset');

it('cannot stores when task_id does not exist in problem_details table', function (array $payload) {
    $this->problemDetail->update(['task_id' => 'different-task-id']);
    $response = $this->postJson(route($this->routeName), $payload);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['task_id'])->toContain('The selected task id is invalid.');
})->with('dataset');

it('cannot store best model with empty payload', function () {
    $response = $this->postJson(route($this->routeName, []));

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['model_path'])->toContain('The model path field is required.')
        ->and($response->json('errors')['model_type'])->toContain('The model type field is required.')
        ->and($response->json('errors')['performance'])->toContain('The performance field is required.')
        ->and($response->json('errors')['task_id'])->toContain('The task id field is required.');
});
it('cannot train if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);
