<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        collect([
            'update user',
            'delete user',
        ])->each(function ($permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        });
    }

    /** @test */
    public function it_updates_a_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('update user');
        $this->actingAs($user); 

        $updatedData = [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@email.com',
            'language' => 'es'
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson(['message' => __('messages.user_updated')]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@email.com',
        ]);
    }

    /** @test */
    public function it_deletes_a_user()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete user');
        $this->actingAs($user); 

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => __('messages.user_deleted')]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function update_returns_404_if_user_not_found()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('update user');
        $this->actingAs($user);

        $response = $this->putJson("/api/users/999999", [
            'name' => 'Test',
            'email' => 'test@email.com'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function delete_returns_404_if_user_not_found()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete user');
        $this->actingAs($user);

        $response = $this->deleteJson("/api/users/999999");

        $response->assertStatus(404);
    }
}
