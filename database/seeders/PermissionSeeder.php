<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'Create User', 'slug' => 'user.create', 'module' => 'User Management'],
            ['name' => 'Edit User', 'slug' => 'user.edit', 'module' => 'User Management'],
            ['name' => 'Delete User', 'slug' => 'user.delete', 'module' => 'User Management'],
            ['name' => 'View User', 'slug' => 'user.view', 'module' => 'User Management'],
            
            // Roles & Access
            ['name' => 'Create Role', 'slug' => 'role.create', 'module' => 'Roles & Access'],
            ['name' => 'Edit Role', 'slug' => 'role.edit', 'module' => 'Roles & Access'],
            ['name' => 'Delete Role', 'slug' => 'role.delete', 'module' => 'Roles & Access'],
            ['name' => 'View Role', 'slug' => 'role.view', 'module' => 'Roles & Access'],
            
            // Audit Logs
            ['name' => 'Create Audit', 'slug' => 'audit.create', 'module' => 'Audit Logs'],
            ['name' => 'Edit Audit', 'slug' => 'audit.edit', 'module' => 'Audit Logs'],
            ['name' => 'Delete Audit', 'slug' => 'audit.delete', 'module' => 'Audit Logs'],
            ['name' => 'View Audit', 'slug' => 'audit.view', 'module' => 'Audit Logs'],
            
            // Billing
            ['name' => 'Create Billing', 'slug' => 'billing.create', 'module' => 'Billing'],
            ['name' => 'Edit Billing', 'slug' => 'billing.edit', 'module' => 'Billing'],
            ['name' => 'Delete Billing', 'slug' => 'billing.delete', 'module' => 'Billing'],
            ['name' => 'View Billing', 'slug' => 'billing.view', 'module' => 'Billing'],
            
            // System Settings
            ['name' => 'Create Settings', 'slug' => 'settings.create', 'module' => 'System Settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'module' => 'System Settings'],
            ['name' => 'Delete Settings', 'slug' => 'settings.delete', 'module' => 'System Settings'],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'System Settings'],
            
            // Reports
            ['name' => 'Create Report', 'slug' => 'report.create', 'module' => 'Reports'],
            ['name' => 'Edit Report', 'slug' => 'report.edit', 'module' => 'Reports'],
            ['name' => 'Delete Report', 'slug' => 'report.delete', 'module' => 'Reports'],
            ['name' => 'View Report', 'slug' => 'report.view', 'module' => 'Reports'],

            // API Tokens
            ['name' => 'Create API Token', 'slug' => 'api.create', 'module' => 'API Tokens'],
            ['name' => 'Edit API Token', 'slug' => 'api.edit', 'module' => 'API Tokens'],
            ['name' => 'Delete API Token', 'slug' => 'api.delete', 'module' => 'API Tokens'],
            ['name' => 'View API Token', 'slug' => 'api.view', 'module' => 'API Tokens'],

            // Menu Management
            ['name' => 'Create Menu', 'slug' => 'menu.create', 'module' => 'Menu Management'],
            ['name' => 'Edit Menu',   'slug' => 'menu.edit',   'module' => 'Menu Management'],
            ['name' => 'Delete Menu', 'slug' => 'menu.delete', 'module' => 'Menu Management'],
            ['name' => 'View Menu',   'slug' => 'menu.view',   'module' => 'Menu Management'],
        ];

        // Make seeding idempotent – don't fail on duplicates
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']], // unique key
                $permission                      // fields to set/update
            );
        }
    }
}
