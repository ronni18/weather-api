<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_registrar_un_usuario()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => __('messages.user_registered'),
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    /** @test */
    public function puede_iniciar_sesion_con_credenciales_validas()
    {
        // Crear un usuario primero
        $user = \App\Models\User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Intentar login
        $response = $this->postJson('/api/login', [
            'email' => 'loginuser@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token'
                ]);
    }

    /** @test */
    public function no_puede_iniciar_sesion_con_credenciales_invalidas()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'badlogin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'badlogin@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(422); // Laravel retorna 422 para errores de validación
    }


}
