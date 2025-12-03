<?php

namespace Tests\Feature;

use App\Models\BestModel;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(function () {
    $this->routeName = 'api.datasets.upsert';

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Storage::fake('datasets');
});

dataset('dataset data', [
    function () {
        $file = UploadedFile::fake()->createWithContent('data.csv', 'id,name,email'.PHP_EOL.'1,Ana,ana@example.com'.PHP_EOL);
        $file->storeAs("{$this->user->id}", 'data.csv', 'datasets');

        $this->dataset = Dataset::factory()->create([
            'path' => $this->user->id.'/'.$file->name,
            'user_id' => $this->user->id,
            'column_names' => 'id,name,email',
            'has_null' => 0,
        ]);

        $this->problemDetail = ProblemDetail::factory()->create([
            'dataset_id' => $this->dataset->id,
        ]);

        BestModel::factory()->create([
            'problem_detail_id' => $this->problemDetail->id,
            'dataset_id' => $this->dataset->id,
        ]);

        return [
            'dataset' => UploadedFile::fake()->createWithContent('data.csv',
                'id,name,email'.PHP_EOL.
                '1,Ana,ana@example.com'.PHP_EOL.
                '2,Bob,bob@example.com'.PHP_EOL
            ),
        ];
    },
]);

it('upserts a dataset', function (array $datasetData) {
    $newRowCount = 0;
    if (($handle = fopen($datasetData['dataset']->getRealPath(), 'r')) !== false) {
        while (fgetcsv($handle) !== false) {
            $newRowCount++;
        }
        fclose($handle);
    }

    $oldRowCount = 0;
    if (($handle = fopen($this->dataset->full_path, 'r')) !== false) {
        while (fgetcsv($handle) !== false) {
            $oldRowCount++;
        }
        fclose($handle);
    }

    assertNotEquals($newRowCount, $oldRowCount);

    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
        'column_names' => 'id,name,email',
    ]);

    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), $datasetData);

    expect($response->status())->toBe(200)
        ->and($response->json())->toHaveKeys(['id', 'has_null', 'name', 'column_names', 'created_at', 'updated_at']);

    $updatedRowCount = 0;
    if (($handle = fopen($this->dataset->full_path, 'r')) !== false) {
        while (fgetcsv($handle) !== false) {
            $updatedRowCount++;
        }
        fclose($handle);
    }

    assertEquals($newRowCount, $updatedRowCount);

    $this->assertDatabaseHas('datasets', [
        'user_id' => $this->user->id,
        'column_names' => 'id,name,email',
    ]);

    Storage::disk('datasets')->assertExists($response->json('data.path'));
})->with('dataset data');

it('cannot upsert with invalid data', function () {
    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), [
        'dataset' => UploadedFile::fake()->create('data.txt', 100, 'text/plain'),
    ]);

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field must be a file of type: csv.')
        ->and($response->json('errors')['dataset'])->toContain('The file needs to have at least two non-empty rows.');
})->with('dataset data');

it('cannot upsert without required parameters', function () {
    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The dataset field is required.');
})->with('dataset data');

it('cannot upsert if user is not authenticated', function () {
    auth()->logout();

    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())->toBe(401)
        ->and($response->json())->toMatchArray([
            'message' => 'Token could not be parsed from the request.',
        ]);
})->throws(JWTException::class);

it('cannot upsert item that does not exist', function () {
    $response = $this->putJson(route($this->routeName, [
        'dataset' => 9999,
    ]));

    expect($response->status())
        ->toBe(404)
        ->and($response->json('error'))->toBe('Not found');
});

it('cannot upsert item that is not created by authenticated user', function () {
    $differentDataset = Dataset::factory()->createQuietly();

    $response = $this->putJson(route($this->routeName, [
        'dataset' => $differentDataset->id,
    ]));

    expect($response->status())
        ->toBe(403)
        ->and($response->json('message'))->toBe('This action is unauthorized.');
});

it('cannot upsert if old dataset is unreadable', function () {
    $response = $this->putJson(route($this->routeName, [
        'dataset' => Dataset::factory()->create()->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('Dataset file is empty or unreadable.');
});

it('cannot upsert if new dataset is empty', function () {
    $newDataset = UploadedFile::fake()->createWithContent('data.csv', '');
    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), [
        'dataset' => $newDataset,
    ]);

    expect($response->status())
        ->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The file needs to have at least two non-empty rows.');
})->with('dataset data');

it('cannot upsert if new dataset has different columns', function () {
    $newDataset = UploadedFile::fake()->createWithContent('data.csv', 'id,name,gender'.PHP_EOL.'1,Ana,f'.PHP_EOL);
    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]), [
        'dataset' => $newDataset,
    ]);

    expect($response->status())
        ->toBe(422)
        ->and($response->json('errors')['dataset'])->toContain('The uploaded CSV must have the same columns as the original dataset.');
})->with('dataset data');

it('cannot upsert if dataset path does not exist', function () {
    $this->dataset->update(['path' => 'non/existent/path.csv']);

    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('The dataset file does not exist.');
})->with('dataset data');

it('cannot upsert if dataset cannot be opened', function () {
    $this->dataset->update(['path' => 'blocked.csv']);
    Storage::disk('datasets')->put('blocked.csv', 'some content');
    $fullPath = Storage::disk('datasets')->path('blocked.csv');
    chmod($fullPath, 0000);

    $response = $this->putJson(route($this->routeName, [
        'dataset' => $this->dataset->id,
    ]));

    expect($response->status())
        ->toBe(422)
        ->and($response->json('error'))->toBe('The dataset file cannot be opened.');
})->with('dataset data');
