<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoriteCity;

class FavoriteCityController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/favorites",
     *      operationId="getFavoriteCities",
     *      tags={"Favorites"},
     *      summary="Obtener ciudades favoritas",
     *      description="Retorna una lista de ciudades favoritas del usuario",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Lista de ciudades favoritas obtenida con éxito")
     * )
     */
    public function index()
    {
        $favorities = auth()->user()->favorites;
        return response()->json($favorities);
    }

    /**
     * @OA\Post(
     *      path="/api/favorites/add",
     *      operationId="addFavoriteCity",
     *      tags={"Favorites"},
     *      summary="Agregar una ciudad a favoritos",
     *      description="Guarda una ciudad en la lista de favoritos del usuario",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"city", "country"},
     *              @OA\Property(property="city", type="string", example="Bogota"),
     *              @OA\Property(property="country", type="string", example="Colombia")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Ciudad agregada a favoritos")
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'country' => 'required|string'
        ]);
    
        $userId = auth()->user()->id;
    
        $exists = FavoriteCity::where('user_id', $userId)
            ->where('city', $request->city)
            ->where('country', $request->country)
            ->exists();
    
        if ($exists) {
            return response()->json(['message' => __('messages.city_already_favorite')], 409);
        }
    
        FavoriteCity::create([
            'user_id' => $userId,
            'city' => $request->city,
            'country' => $request->country
        ]);
    
        return response()->json(['message' => __('messages.city_added')], 201);
    }
     

    /**
     * @OA\Delete(
     *      path="/api/favorites/remove",
     *      operationId="removeFavoriteCity",
     *      tags={"Favorites"},
     *      summary="Eliminar una ciudad de favoritos",
     *      description="Elimina una ciudad de la lista de favoritos del usuario",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"IdFavorite"},
     *              @OA\Property(property="IdFavorite", type="string", example="10")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Ciudad eliminada de favoritos")
     * )
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'IdFavorite' => 'required|integer'
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => __('messages.unauthenticated')], 401);
        }

        $favorite = FavoriteCity::where('id', $request->IdFavorite)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$favorite) {
            return response()->json(['message' => __('messages.favorite_not_found')], 404);
        }

        $favorite->delete();

        return response()->json(['message' => __('messages.city_removed')]);
    }

}
