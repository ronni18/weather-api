<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Weather API",
 *      description="API para consultar el clima y gestionar usuarios",
 *      @OA\Contact(
 *          email="tuemail@example.com"
 *      )
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Autenticación de usuarios"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="registerUser",
     *      tags={"Auth"},
     *      summary="Registrar un nuevo usuario",
     *      description="Crea una cuenta nueva en el sistema",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="language", type="string", example="es"),
     *          )
     *      ),
     *      @OA\Response(response=201, description="Usuario registrado con éxito"),
     *      @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'language' => $request->language,

        ]);

        return response()->json(['message' => __('messages.user_registered')], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="loginUser",
     *      tags={"Auth"},
     *      summary="Iniciar sesión",
     *      description="Autentica un usuario y devuelve un token de acceso",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Inicio de sesión exitoso"),
     *      @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => __('messages.invalid_credentials')]);
        }

        return response()->json(['token' => $user->createToken('auth_token')->plainTextToken]);
    }
}
