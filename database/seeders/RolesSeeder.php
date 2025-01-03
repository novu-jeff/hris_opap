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
        // Define roles and their descriptions
        $roles = [
            [
                'name' => 'superadmin',
                'description' => 'A superadmin has full control over system settings and user management.',
            ],
            [
                'name' => 'admin',
                'description' => 'An admin manages system settings and user permissions with high-level access and control.',
            ],
        ];

        // Define permissions for each role
        $permissions = [
            'superadmin' => Permission::pluck('name')->toArray(),
            'admin' => Permission::whereNotIn('name', [
                'read roles',
                'write roles',
            ])->pluck('name')->toArray(), // Exclude certain permissions
        ];

        // Loop through roles and either create or update them
        foreach ($roles as $roleData) {
            // Find or create the role
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Sync permissions for the role
            if (isset($permissions[$roleData['name']])) {
                $role->syncPermissions($permissions[$roleData['name']]);
            }
        }
    }
}
