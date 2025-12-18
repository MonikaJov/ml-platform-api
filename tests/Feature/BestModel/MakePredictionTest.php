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

// NOTE: Make sure ml engine server is running before executing these tests.

beforeEach(function () {
    $this->routeName = 'api.dataset.problem-detail.best-models.predict';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

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
            'column_names' => 'id,name,gender',
        ]);

        $this->problemDetail = ProblemDetail::factory()->create([
            'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
            'target_column' => 'gender',
            'dataset_id' => $this->dataset->id,
        ]);

        $this->bestModel = BestModel::factory()->create([
            'problem_detail_id' => $this->problemDetail->id,
            'path' => 'test_model', // Ensure it exists in the ML engine for the test
        ]);
    },
]);

it('successfully makes a prediction', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
        'best_model' => $this->bestModel->id,
    ]), [
        'data' => [
            'id' => 4,
            'name' => 'David',
        ],
    ]);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['gender'])
        ->and($response->json('gender'))->toBe('m');
})->with('dataset')->skip();

it('cannot make a prediction if dataset or problem detail or best model do not exist', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => 9999,
        'problem_detail' => 9999,
        'best_model' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot  make a prediction if problem detail does not belong to dataset', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
        'best_model' => BestModel::factory()->create()->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('BestModel does not belong to the given ProblemDetail.');
})->with('dataset');

it('cannot make a prediction if best model does not belong to problem detail', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => ProblemDetail::factory()->create()->id,
        'best_model' => $this->bestModel->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('ProblemDetail does not belong to the given Dataset.');
})->with('dataset');

it('cannot make a prediction if a column does not exist in the dataset', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
        'best_model' => $this->bestModel->id,
    ]), [
        'data' => [
            'id' => 4,
            'surname' => 'David',
        ],
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors')['data.surname'])->toContain('Column "surname" does not exist in dataset.');
})->with('dataset');

it('cannot train if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);
