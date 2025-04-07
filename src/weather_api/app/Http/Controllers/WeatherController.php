<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;


class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * @OA\Get(
     *      path="/api/weather/{city}",
     *      operationId="getWeather",
     *      tags={"Weather"},
     *      summary="Obtener clima de una ciudad",
     *      description="Retorna la temperatura, humedad y otros datos del clima",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="city",
     *          description="Nombre de la ciudad",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response=200, description="Clima obtenido con éxito"),
     *      @OA\Response(response=400, description="Error en la solicitud"),
     *      @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function getWeather($city)
    {
        $weather = $this->weatherService->getCurrentWeather($city);
                
        return $weather;
    }

    /**
     * @OA\Get(
     *      path="/api/weather-history",
     *      operationId="getHistoryCities",
     *      tags={"Histories"},
     *      summary="Obtener historial de ciudades",
     *      description="Retorna una lista del historial de ciudades del usuario",
     *      security={{"sanctum":{}}},
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Lista de historial de ciudades obtenida con éxito")
     * )
     */
    public function getHistory()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => __('messages.unauthenticated')], 401);
        }

        $history = $user->histories;

        return response()->json($history);
    }

}
