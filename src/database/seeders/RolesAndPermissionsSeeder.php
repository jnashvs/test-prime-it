<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'assign appointments',
            'view all appointments',
            'create appointments',
            'edit appointments',
            'delete appointments',
            'create pets',
            'edit pets',
            'delete pets',
            'view all pets',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'sanctum']
            );
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $roles = [
            'doctor' => [
                'view all appointments',
                'edit appointments',
            ],
            'receptionist' => [
                'view all appointments',
                'assign appointments',
                'create appointments',
                'edit appointments',
                'delete appointments',
                'view all pets',
            ],
            'user' => [
                'view all appointments',
                'create appointments',
                'edit appointments',
                'view all pets',
                'create pets',
                'edit pets',
                'delete pets',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $role => $rolePermissions) {
            Role::create(['name' => $role, 'guard_name' => 'sanctum'])
                ->givePermissionTo($rolePermissions);
            Role::create(['name' => $role, 'guard_name' => 'web'])
                ->givePermissionTo($rolePermissions);
        }
    }

}
