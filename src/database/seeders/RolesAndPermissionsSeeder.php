<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
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

        // Define roles and their permissions
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
