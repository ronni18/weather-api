<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FavoriteCityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Crear permisos necesarios
        collect([
            'view favorites',
            'add favorite',
            'remove favorite',
        ])->each(function ($permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        });

        // Crear y autenticar un usuario por defecto
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_favorite_cities()
    {
        $this->user->givePermissionTo('view favorites');
        $this->actingAs($this->user);

        FavoriteCity::factory()->create([
            'user_id' => $this->user->id,
            'city' => 'Bogotá',
            'country' => 'Colombia',
        ]);

        $response = $this->getJson('/api/favorites');

        $response->assertStatus(200)
                 ->assertJsonFragment(['city' => 'Bogotá']);
    }

    public function test_user_can_add_favorite_city()
    {
        $this->user->givePermissionTo('add favorite');
        $this->actingAs($this->user);

        $response = $this->postJson('/api/favorites/add', [
            'city' => 'Lima',
            'country' => 'Perú',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => __('messages.city_added')]);
    }

    public function test_user_cannot_add_duplicate_favorite_city()
    {
        $this->user->givePermissionTo('add favorite');
        $this->actingAs($this->user);
        FavoriteCity::factory()->create([
            'user_id' => $this->user->id,
            'city' => 'Quito',
            'country' => 'Ecuador',
        ]);

        $response = $this->postJson('/api/favorites/add', [
            'city' => 'Quito',
            'country' => 'Ecuador',
        ]);

        $response->assertStatus(409)
                 ->assertJson(['message' => __('messages.city_already_favorite')]);
    }

    public function test_user_can_remove_favorite_city()
    {
        $this->user->givePermissionTo('remove favorite');
        $this->actingAs($this->user);

        $favorite = FavoriteCity::factory()->create([
            'user_id' => $this->user->id,
            'city' => 'Madrid',
            'country' => 'España',
        ]);

        $response = $this->deleteJson('/api/favorites/remove', [
            'IdFavorite' => $favorite->id,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => __('messages.city_removed')]);
    }
}
