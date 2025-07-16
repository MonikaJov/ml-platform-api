<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DatasetFactory extends Factory
{
    protected $model = Dataset::class;

    public function definition(): array
    {
        $filename = $this->faker->unique()->lexify('dataset_????.csv');

        return [
            'path' => 'uploads/'.$filename,
            'user_id' => User::factory(),
            'has_null' => $this->faker->boolean(30),
            'created_at' => Carbon::now(),
        ];
    }
}
