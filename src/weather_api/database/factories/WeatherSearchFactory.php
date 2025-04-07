<?php

namespace Database\Factories;

use App\Models\WeatherSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeatherSearchFactory extends Factory
{
    protected $model = WeatherSearch::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'city' => $this->faker->city,
            'country' => $this->faker->country,
        ];
    }
}
