<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FavoriteCity;

class FavoriteCitySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $firstSearch = $user->histories->first();

            if ($firstSearch) {
                FavoriteCity::create([
                    'user_id' => $user->id,
                    'city' => $firstSearch->city,
                    'country' => $firstSearch->country,
                ]);

                $this->command->info("Favorito creado para {$user->name}: {$firstSearch->city}, {$firstSearch->country}");
            } else {
                $this->command->warn("El usuario {$user->name} no tiene historiales.");
            }
        }
    }
}
