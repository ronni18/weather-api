<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {

        $permisos = [
            'view weather',
            'view history',
            'add favorite',
            'remove favorite',
            'view favorites',
            'view users',
            'update user',
            'delete user',
            'create role',
            'delete role',
            'view roles',
            'create permission',
            'delete permission',
            'view permissions',
            'assign role',
            'assign permission',
        ];
        

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $consultant = Role::firstOrCreate(['name' => 'consultant']);

        $admin->syncPermissions(Permission::all());

        $consultant->syncPermissions([
            'view weather',
            'view history',
            'add favorite',
            'remove favorite',
            'view favorites',
        ]);
    }
}

