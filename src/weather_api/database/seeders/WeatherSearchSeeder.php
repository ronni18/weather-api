<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherSearch;
use App\Models\User;

class WeatherSearchSeeder extends Seeder
{
    public function run()
    {

        $users = User::all();

        if ($users->count() < 3) {
            $this->command->warn('Se requieren al menos 3 usuarios. Ejecuta UserSeeder primero.');
            return;
        }

        $cities = [
            ['city' => 'Bogotá', 'country' => 'Colombia'],
            ['city' => 'Lima', 'country' => 'Perú'],
            ['city' => 'Quito', 'country' => 'Ecuador'],
            ['city' => 'Buenos Aires', 'country' => 'Argentina'],
            ['city' => 'Santiago', 'country' => 'Chile'],
            ['city' => 'Caracas', 'country' => 'Venezuela'],
            ['city' => 'Ciudad de México', 'country' => 'México'],
            ['city' => 'Madrid', 'country' => 'España'],
            ['city' => 'Miami', 'country' => 'Estados Unidos'],
            ['city' => 'Toronto', 'country' => 'Canadá'],
        ];

        $chunks = array_chunk($cities, ceil(count($cities) / 3));

        foreach ($users->take(3)->values() as $index => $user) {
            $group = $chunks[$index] ?? [];
            foreach ($group as $data) {
                WeatherSearch::create([
                    'user_id' => $user->id,
                    'city'    => $data['city'],
                    'country' => $data['country'],
                ]);
            }
        }

        $this->command->info('Historiales de búsqueda asignados por usuario.');
    }
}
