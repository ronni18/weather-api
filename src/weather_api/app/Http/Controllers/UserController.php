<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
     /**
     * @OA\Get(
     *      path="/api/users",
     *      operationId="GetUsers",
     *      tags={"Users"},
     *      summary="Obtener Usuarios",
     *      description="Retorna una lista de los usuarios",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Lista de usuarios obtenida con éxito")
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      operationId="updateUser",
     *      tags={"Users"},
     *      summary="Actualizar datos del usuario",
     *      description="Actualiza el nombre y correo electrónico del usuario",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Updated"),
     *              @OA\Property(property="email", type="string", format="email", example="johnupdated@example.com"),
     *              @OA\Property(property="language", type="string", example="es"),
     *          )
     *      ),
     *      @OA\Response(response=200, description="Usuario actualizado correctamente"),
     *      @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only('name', 'email', 'language'));

        return response()->json(['message' => __('messages.user_updated')]);
    }

    /**
     * @OA\Delete(
     *      path="/api/users/{id}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Eliminar usuario",
     *      description="Elimina un usuario del sistema",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response=200, description="Usuario eliminado correctamente"),
     *      @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => __('messages.user_not_found')], 404);
        }

        $user->delete();

        return response()->json(['message' => __('messages.user_deleted')]);
    }
}
