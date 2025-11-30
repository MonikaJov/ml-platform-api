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
    $this->routeName = 'api.datasets.index';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('datasets');
});

dataset('dataset data', [
    function () {
        $file1 = UploadedFile::fake()->createWithContent('data1.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,1'.PHP_EOL);
        $file1->storeAs("{$this->user->id}", 'data1.csv', 'datasets');
        $this->dataset1 = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file1->name,
            'column_names' => 'id,name,email,gender',
            'user_id' => $this->user->id,
        ]);
        $this->bestModel = BestModel::factory()->create([
            'dataset_id' => $this->dataset1->id,
            'problem_detail_id' => ProblemDetail::factory()->create([
                'dataset_id' => $this->dataset1->id,
            ])->id,
        ]);

        $file2 = UploadedFile::fake()->createWithContent('data2.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,1'.PHP_EOL);
        $file2->storeAs("{$this->user->id}", 'data2.csv', 'datasets');
        $this->dataset2 = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file2->name,
            'column_names' => 'id,name,email,gender',
            'user_id' => $this->user->id,
        ]);
        $this->bestModel = BestModel::factory()->create([
            'dataset_id' => $this->dataset2->id,
            'problem_detail_id' => ProblemDetail::factory()->create([
                'dataset_id' => $this->dataset2->id,
            ])->id,
        ]);

        $file3 = UploadedFile::fake()->createWithContent('data3.csv', 'id,name,email,gender'.PHP_EOL.'1,Ana,ana@example.com,1'.PHP_EOL);
        $anotherUser = User::factory()->create();
        $file3->storeAs("{$anotherUser->id}", 'data3.csv', 'datasets');

        $this->dataset3 = Dataset::factory()->createQuietly([
            'path' => $anotherUser->id.'/'.$file3->name,
            'column_names' => 'id,name,email,gender',
            'user_id' => $anotherUser->id,
        ]);
    },
]);

it('lists datasets without filters', function () {
    $this->assertDatabaseCount('datasets', 3);

    $response = $this->getJson(route($this->routeName));

    expect($response->status())->toBe(200)
        ->and($response->json('total_records'))->toBe(2)
        ->and($response->json('data')[0])->toHaveKeys([
            'id',
            'problem_details',
            'name',
            'has_null',
            'name',
            'column_names',
            'created_at',
            'updated_at',
        ])
        ->and($response->json('data')[0]['problem_details'])->toHaveKeys([
            'id',
            'type',
            'target_column',
            'best_model',
            'task_id',
            'created_at',
            'updated_at',
        ])
        ->and($response->json('data')[0]['problem_details']['best_model'])->toHaveKeys([
            'id',
            'path',
            'name',
            'performance',
            'created_at',
            'updated_at',
        ]);
})->with('dataset data');

it('lists datasets with id filter', function () {
    $dataset = Dataset::get()->first();

    $response = $this->getJson(route($this->routeName, [
        'filter' => [
            'id' => $dataset->id,
        ],
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json('total_records'))->toBe(1)
        ->and($response->json('filter'))->toHaveKey('id')
        ->and($response->json('filter')['id'])->toBe((string) $dataset->id)
        ->and($response->json('data')[0]['id'])->toBe($dataset->id);
})->with('dataset data');

it('returns an empty array when an id that does not exist is sent', function () {
    $response = $this->getJson(route($this->routeName, [
        'filter' => [
            'id' => 9999,
        ],
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json('total_records'))->toBe(0)
        ->and($response->json('data'))
        ->toBeEmpty();
})->with('dataset data');

it('lists datasets with name filter', function () {
    $dataset = Dataset::get()->first();

    $response = $this->getJson(route($this->routeName, [
        'filter' => [
            'name' => $dataset->name,
        ],
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json('data'))->toHaveCount(1)
        ->and($response->json('filter'))->toHaveKey('name')
        ->and($response->json('filter')['name'])->toBe((string) $dataset->name)
        ->and($response->json('data')[0]['name'])->toBe($dataset->name);
})->with('dataset data');

it('returns an empty array when a name that does not exist is sent', function () {
    $response = $this->getJson(route($this->routeName, [
        'filter' => [
            'name' => 'non-existent-name',
        ],
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json('total_records'))->toBe(0)
        ->and($response->json('data'))
        ->toBeEmpty();
})->with('dataset data');

it('validates filters', function () {
    $response = $this->getJson(route($this->routeName, [
        'filter' => [
            'id' => 'invalid',
        ],
    ]));

    expect($response->status())->toBe(422)
        ->and($response->json())->toHaveKeys(['message', 'errors'])
        ->and($response->json('errors'))->toHaveKeys(['filter.id']);
})->with('dataset data');

it('can sort datasets', function () {
    $dataset = Dataset::orderBy('id')->first();

    $response = $this->getJson(route($this->routeName, [
        'sort' => [
            'id',
        ],
    ]));

    expect($response->status())->toBe(200)
        ->and($response->json('total_records'))->toBe(2)
        ->and($response->json()['data'])->toBeArray()
        ->and($response->json()['data'][0]['id'])->toBe($dataset->id);
})->with('dataset data');

it('validates sort', function () {
    $response = $this->getJson(route($this->routeName, [
        'sort' => [
            'invalid',
        ],
    ]));
    expect($response->status())->toBe(422)
        ->and($response->json('errors')['sort.0'])->toContain('The selected sort.0 is invalid.');
});

it('cannot list if user is not authenticated', function () {
    auth()->logout();

    $response = $this->postJson(route($this->routeName));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);
