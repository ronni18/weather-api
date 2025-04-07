<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeatherSearch;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        collect([
            'view weather',
            'view history',
        ])->each(function ($permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        });

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_returns_weather_data_for_city_when_authenticated()
    {
        $this->user->givePermissionTo('view weather');
        $this->actingAs($this->user);

        $response = $this->getJson('/api/weather/Bogota');
        // dd($response->getStatusCode());

        $response->assertStatus(200)
                 ->assertJson(['message' => __('messages.weather_success')]);
    }

    /** @test */
    public function it_fails_to_get_weather_data_if_unauthenticated()
    {
        $response = $this->getJson('/api/weather/Bogota');

        $response->assertStatus(401)
                 ->assertJson(['message' => __('messages.unauthenticated')]);
    }

    /** @test */
    public function it_returns_weather_history_for_authenticated_user()
    {
        $this->user->givePermissionTo('view history');
        $this->actingAs($this->user);

        WeatherSearch::factory()->create([
            'user_id' => $this->user->id,
            'city' => 'Bogota',
            'country' => 'Colombia'
        ]);

        $response = $this->getJson('/api/weather-history');

        $response->assertStatus(200)
                ->assertJsonFragment(['city' => 'Bogota']);
    }

    /** @test */
    public function it_fails_to_get_history_if_unauthenticated()
    {
        $response = $this->getJson('/api/weather-history');

        $response->assertStatus(401)
                 ->assertJson(['message' => __('messages.unauthenticated')]);
    }
}
