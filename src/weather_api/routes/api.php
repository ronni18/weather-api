<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\FavoriteCityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Weather
    Route::get('/weather/{city}', [WeatherController::class, 'getWeather'])->middleware('can:view weather');
    Route::get('/weather-history', [WeatherController::class, 'getHistory'])->middleware('can:view history');

    // Favorites
    Route::get('/favorites', [FavoriteCityController::class, 'index'])->middleware('can:view favorites');
    Route::post('/favorites/add', [FavoriteCityController::class, 'store'])->middleware('can:add favorite');
    Route::delete('/favorites/remove', [FavoriteCityController::class, 'destroy'])->middleware('can:remove favorite');

    // Users
    Route::get('/users', [UserController::class, 'index'])->middleware('can:view users');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('can:update user');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('can:delete user');

    // Roles y Permisos
    Route::post('/roles', [RolePermissionController::class, 'createRole'])->middleware(['role:admin', 'can:create role']);
    Route::get('/roles', [RolePermissionController::class, 'getRoles'])->middleware(['role:admin', 'can:view roles']);
    Route::delete('/roles/{id}', [RolePermissionController::class, 'deleteRole'])->middleware(['role:admin', 'can:delete role']);

    Route::post('/permissions', [RolePermissionController::class, 'createPermission'])->middleware(['role:admin','can:create permission']);
    Route::get('/permissions', [RolePermissionController::class, 'getPermissions'])->middleware(['role:admin','can:view permissions']);
    Route::delete('/permissions/{id}', [RolePermissionController::class, 'deletePermission'])->middleware(['role:admin','can:delete permission']);

    Route::post('/assign-role', [RolePermissionController::class, 'assignRoleToUser'])->middleware(['role:admin','can:assign role']);
    Route::post('/assign-permission', [RolePermissionController::class, 'assignPermissionToRole'])->middleware(['role:admin','can:assign permission']);
});
