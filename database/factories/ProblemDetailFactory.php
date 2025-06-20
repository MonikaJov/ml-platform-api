<?php

namespace Database\Factories;

use App\Enums\ProblemDetailTypeEnum;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProblemDetailFactory extends Factory
{
    protected $model = ProblemDetail::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(ProblemDetailTypeEnum::cases()),
            'target_column' => $this->faker->word(),
            'dataset_id' => Dataset::factory(),
            'created_at' => Carbon::now(),
        ];
    }
}
