<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName.$this->faker->randomNumber(4),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => null,
            'password' => bcrypt('password'),
            'full_name' => $this->faker->firstName,
            'mobile' => $this->faker->phoneNumber,
            'remember_token' => Str::random(10),
            'created_at' => now(),
        ];
    }
}
