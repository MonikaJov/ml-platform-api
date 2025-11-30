<?php

namespace Tests\Feature;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

// NOTE: Make sure ml engine server is running before executing these tests.

beforeEach(function () {
    $this->routeName = 'api.dataset.problem-detail.best-models.train';

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
        ]);

        $this->problemDetail = ProblemDetail::factory()->create([
            'type' => ProblemDetailTypeEnum::CLASSIFICATION->value,
            'target_column' => 'gender',
            'dataset_id' => $this->dataset->id,
        ]);
    },
]);

it('successfully starts training a model', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]));

    expect($response->status())->toBe(202)
        ->and($response->json())->toHaveKeys(['message', 'code', 'details'])
        ->and($response->json('message'))->toBe('Training job successfully started.')
        ->and($response->json('code'))->toBe(202)
        ->and($response->json('details'))->toHaveKeys(['task_id', 'status']);
})->with('dataset')->skip();

it('cannot train if dataset or problem detail do not exist', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => 9999,
        'problem_detail' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot train if problem detail does not belong to dataset', function () {
    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => ProblemDetail::factory()->create()->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('ProblemDetail does not belong to the given Dataset.');
})->with('dataset');

it('cannot train if dataset path does not exist', function () {
    $this->dataset->update(['path' => 'non/existent/path.csv']);

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('The dataset file does not exist.');
})->with('dataset');

it('cannot train if dataset cannot be opened', function () {
    $this->dataset->update(['path' => 'blocked.csv']);
    Storage::disk('datasets')->put('blocked.csv', 'some content');
    $fullPath = Storage::disk('datasets')->path('blocked.csv');
    chmod($fullPath, 0000);

    $response = $this->postJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
        'problem_detail' => $this->problemDetail->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('The dataset file cannot be opened.');
})->with('dataset');

it('cannot train if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);
