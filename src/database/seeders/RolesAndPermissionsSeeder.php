<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
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

        // Create permissions if they don't exist
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

        // Create roles (if they don't exist) and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            // Create or retrieve the role for 'sanctum' guard
            $sanctumRole = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'sanctum']);
            $sanctumRole->givePermissionTo($rolePermissions);

            // Create or retrieve the role for 'web' guard
            $webRole = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $webRole->givePermissionTo($rolePermissions);
        }
    }
}
