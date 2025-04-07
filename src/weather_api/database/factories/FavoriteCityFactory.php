<?php

namespace Database\Factories;

use App\Models\FavoriteCity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteCityFactory extends Factory
{
    protected $model = FavoriteCity::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'city' => $this->faker->city,
        ];
    }
}
