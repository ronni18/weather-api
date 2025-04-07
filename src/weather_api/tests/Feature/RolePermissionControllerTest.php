<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        collect([
            'view roles',
            'create role',
            'delete role',
            'assign role',
            'view permissions',
            'create permission',
            'delete permission',
            'assign permission',
        ])->each(function ($permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        });

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->user = User::factory()->create();

        $this->user->assignRole($adminRole);
    }

    public function test_create_role()
    {
        $this->user->givePermissionTo('create role');
        $this->actingAs($this->user);

        $response = $this->postJson('/api/roles', ['name' => 'editor']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.role_created')]);

        $this->assertDatabaseHas('roles', ['name' => 'editor']);
    }

    public function test_get_roles()
    {
        $this->user->givePermissionTo('view roles');
        $this->actingAs($this->user);

        Role::create(['name' => 'test-role']);
        $response = $this->getJson('/api/roles');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'test-role']);
    }

    public function test_delete_role()
    {
        $this->user->givePermissionTo('delete role');
        $this->actingAs($this->user);

        $role = Role::create(['name' => 'deleteme']);

        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.role_deleted')]);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_create_permission()
    {
        $this->user->givePermissionTo('create permission');
        $this->actingAs($this->user);
        $response = $this->postJson('/api/permissions', ['name' => 'edit post']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.permission_created')]);

        $this->assertDatabaseHas('permissions', ['name' => 'edit post']);
    }

    public function test_get_permissions()
    {
        $this->user->givePermissionTo('view permissions');
        $this->actingAs($this->user);

        Permission::create(['name' => 'test-permission']);
        $response = $this->getJson('/api/permissions');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'test-permission']);
    }

    public function test_delete_permission()
    {
        $this->user->givePermissionTo('delete permission');
        $this->actingAs($this->user);

        $permission = Permission::create(['name' => 'delete-me']);

        $response = $this->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.permission_deleted')]);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_assign_role_to_user()
    {
        $this->user->givePermissionTo('assign role');
        $this->actingAs($this->user);

        $role = Role::create(['name' => 'tester']);
        $user = User::factory()->create();

        $response = $this->postJson('/api/assign-role', [
            'user_id' => $user->id,
            'role' => 'tester'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.role_assigned')]);

        $this->assertTrue($user->fresh()->hasRole('tester'));
    }

    public function test_assign_permission_to_role()
    {
        $this->user->givePermissionTo('assign permission');
        $this->actingAs($this->user);

        $role = Role::create(['name' => 'manager']);
        $permission = Permission::create(['name' => 'manage users']);

        $response = $this->postJson('/api/assign-permission', [
            'role' => 'manager',
            'permission' => 'manage users'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => __('messages.permission_assigned')]);

        $this->assertTrue($role->fresh()->hasPermissionTo('manage users'));
    }
}
