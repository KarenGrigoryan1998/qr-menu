<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Restaurant management
            'view-restaurants',
            'create-restaurants',
            'edit-restaurants',
            'delete-restaurants',

            // Menu management
            'view-menus',
            'create-menus',
            'edit-menus',
            'delete-menus',

            // Category management
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // Table management
            'view-tables',
            'create-tables',
            'edit-tables',
            'delete-tables',

            // Order management
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',

            // Payment management
            'view-payments',
            'create-payments',
            'edit-payments',
            'delete-payments',

            // User management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Waiter Request management
            'view-waiter-requests',
            'create-waiter-requests',
            'edit-waiter-requests',
            'delete-waiter-requests',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $owner = Role::create(['name' => 'owner']);
        $owner->givePermissionTo([
            'view-menus', 'create-menus', 'edit-menus', 'delete-menus',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-tables', 'create-tables', 'edit-tables', 'delete-tables',
            'view-orders', 'edit-orders',
            'view-payments',
            'view-users', 'create-users', 'edit-users',
            'view-waiter-requests', 'edit-waiter-requests',
        ]);

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-menus', 'edit-menus',
            'view-categories',
            'view-tables', 'edit-tables',
            'view-orders', 'edit-orders',
            'view-payments',
            'view-waiter-requests', 'edit-waiter-requests',
        ]);

        $waiter = Role::create(['name' => 'waiter']);
        $waiter->givePermissionTo([
            'view-menus',
            'view-categories',
            'view-tables',
            'view-orders', 'create-orders', 'edit-orders',
            'view-waiter-requests', 'edit-waiter-requests',
        ]);
    }
}
