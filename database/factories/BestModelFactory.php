<?php

namespace Database\Factories;

use App\Models\BestModel;
use App\Models\ProblemDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BestModelFactory extends Factory
{
    protected $model = BestModel::class;

    public function definition(): array
    {
        $filename = $this->faker->unique()->lexify('model_??????.pkl');

        return [
            'path' => 'models/'.$filename.'.pkl',
            'name' => $this->faker->word.'Model',
            'problem_detail_id' => ProblemDetail::factory(),
            'performance' => '{"accuracy": '.$this->faker->randomFloat(4, 0.7, 1.0).', "f1_score": '.$this->faker->randomFloat(4, 0.7, 1.0).'}',
            'created_at' => Carbon::now(),
        ];
    }
}
