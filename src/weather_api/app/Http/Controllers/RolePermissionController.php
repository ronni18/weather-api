<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     operationId="createRole",
     *     tags={"Roles"},
     *     summary="Crear un nuevo rol",
     *     description="Crea un nuevo rol en el sistema",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="editor")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol creado exitosamente"),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $role = Role::create(['name' => $request->name]);

        return response()->json(['message' => ['message' => __('messages.role_created')], 'role' => $role], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     operationId="getRoles",
     *     tags={"Roles"},
     *     summary="Listar todos los roles",
     *     description="Devuelve todos los roles registrados en el sistema",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de roles")
     * )
     */
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     operationId="deleteRole",
     *     tags={"Roles"},
     *     summary="Eliminar un rol",
     *     description="Elimina un rol por ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Rol eliminado correctamente"),
     *     @OA\Response(response=404, description="Rol no encontrado")
     * )
     */
    public function deleteRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' =>  __('messages.user_not_found')], 404);
        }

        $role->delete();

        return response()->json(['message' =>  __('messages.role_deleted')]);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     operationId="createPermission",
     *     tags={"Permisos"},
     *     summary="Crear un nuevo permiso",
     *     description="Crea un nuevo permiso en el sistema",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="edit post")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permiso creado exitosamente"),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function createPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json(['message' =>  __('messages.permission_created'), 'permission' => $permission], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     operationId="getPermissions",
     *     tags={"Permisos"},
     *     summary="Listar todos los permisos",
     *     description="Devuelve todos los permisos registrados en el sistema",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de permisos")
     * )
     */
    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * @OA\Delete(
     *     path="/api/permissions/{id}",
     *     operationId="deletePermission",
     *     tags={"Permisos"},
     *     summary="Eliminar un permiso",
     *     description="Elimina un permiso por ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Permiso eliminado correctamente"),
     *     @OA\Response(response=404, description="Permiso no encontrado")
     * )
     */
    public function deletePermission($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['error' => __('messages.permission_not_found')], 404);
        }

        // Verificar si el permiso tiene relaciones con roles o usuarios
        if ($permission->roles()->exists() || $permission->users()->exists()) {
            return response()->json(['error' => __('messages.permission_in_use')], 400);
        }

        $permission->delete();

        return response()->json(['message' => __('messages.permission_deleted')]);
    }


    /**
     * @OA\Post(
     *     path="/api/assign-role",
     *     operationId="assignRoleToUser",
     *     tags={"Roles"},
     *     summary="Asignar un rol a un usuario",
     *     description="Asigna un rol específico a un usuario",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "role"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rol asignado correctamente"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function assignRoleToUser(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['error' =>  __('messages.user_not_found')], 404);
        }

        $user->assignRole($request->role);

        return response()->json(['message' =>  __('messages.role_assigned')]);
    }

    /**
     * @OA\Post(
     *     path="/api/assign-permission",
     *     operationId="assignPermissionToRole",
     *     tags={"Permisos"},
     *     summary="Asignar un permiso a un rol",
     *     description="Asigna un permiso a un rol específico",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role", "permission"},
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="permission", type="string", example="view weather")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permiso asignado al rol correctamente"),
     *     @OA\Response(response=404, description="Rol o permiso no encontrado")
     * )
     */
    public function assignPermissionToRole(Request $request)
    {
        $role = Role::where('name', $request->role)->first();
        $permission = Permission::where('name', $request->permission)->first();

        if (!$role || !$permission) {
            return response()->json(['error' =>  __('messages.role_or_permission_not_found')], 404);
        }

        $role->givePermissionTo($permission);

        return response()->json(['message' =>  __('messages.permission_assigned')]);
    }
}
