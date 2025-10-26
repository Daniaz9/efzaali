<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            // === User Management ===
            'view users',
            'create users',
            'edit users',
            'delete users',

            // === Orders ===
            'view orders',
            'create orders',
            'cancel orders',
            'update order status', // for drivers
            'assign driver to orders',

            // === Offers ===
            'view offers',
            'create offers',
            'accept offers',

            // === Ratings ===
            'rate user',

            // === Drivers ===
            'change availability',

            // === Admin / System ===
            'manage system settings',
            'view dashboard',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'sanctum']);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $driver     = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'sanctum']);
        $customer   = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'sanctum']);

        // Assign permissions to roles
        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view orders',
            'view offers',
            'accept offers',
            'rate user',
            'manage system settings',
            'view dashboard',
        ]);

        $driver->syncPermissions([
            'view orders',
            'update order status',
            'change availability',
            'create offers',
            'view offers',
            'rate user',
        ]);

        $customer->syncPermissions([
            'create orders',
            'cancel orders',
            'view orders',
            'rate user',
            'assign driver to orders',
        ]);
    }
}
