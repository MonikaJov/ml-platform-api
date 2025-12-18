<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class DatasetFactory extends Factory
{
    protected $model = Dataset::class;

    public function definition(): array
    {
        $filename = $this->faker->unique()->lexify('dataset_????.csv');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create($filename, 100, 'csv');
        $path = $file->storeAs("{$user->id}", $filename, 'datasets');

        return [
            'path' => $path,
            'user_id' => $user->id,
            'column_names' => '',
            'created_at' => Carbon::now(),
        ];
    }
}
