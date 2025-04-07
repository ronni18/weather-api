<?php

namespace App\Services;

use App\Models\WeatherSearch;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('weather.base_url');
        $this->apiKey = config('weather.api_key');
    }

    public function getCurrentWeather($city)
    {
        return Cache::remember("weather_{$city}", 300, function () use ($city) {
            $response = Http::get("{$this->baseUrl}/current.json", [
                'key' => $this->apiKey,
                'q'   => $city,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                $data = [
                    'country'     => $result['location']['country'],
                    'city'        => $result['location']['name'],
                    'temperature' => $result['current']['temp_c'],
                    'weather'     => $result['current']['condition']['text'],
                    'wind'        => $result['current']['wind_kph'],
                    'humidity'    => $result['current']['humidity'],
                    'local_time'  => $result['location']['localtime'],
                ];                
                
                $this->saveHistory($data);
        
                return response()->json($data, 200);

            }

            return response()->json(['message' => __('messages.weather_error')], 400);
        });
    }

    protected function saveHistory($data)
    {
        WeatherSearch::create([
            'user_id' => auth()->user()->id,
            'city' => $data['city'],
            'country' => $data['country'],
        ]);
    }
}
