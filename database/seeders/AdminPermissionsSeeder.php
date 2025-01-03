<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'recruitment' => [
                'jobs',
                'applicants'
            ],
            'hris' => [
                'hris'
            ],
            'timekeeping' => [
                'timelogs',
                'correction-timelogs'
            ],
            'payroll' => [

            ],
            'ess' => [
                'leave',
                'obs',
                'arto',
                'announcements',
                'employee-profile-approval',
                'request-status',
            ],
            'reports' => [
                'dtr'
            ],
            'settings' => [
                'company-information',
                'branches',
                'departments',
                'sections',
                'assessments',
                'requirements',
                'users',
                'roles',
                'bank-information',
                'batch-configuration',
                'employment-type',
                'positions',
                'violations',
                'leave-types',
                'gsis-billing',
                'employee-deductions',
                'other-earnings',
                'other-deductions',
                'shift-schedule',
                'employee-schedule',
                'holidays',
                'payroll-period',
                'payroll-configuration'
            ]
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                $this->createPermission($module, "read $action");
                $this->createPermission($module, "write $action");
            }
        }
    }

    private function createPermission(string $module, string $permissionName)
    {
        Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web', 
            'module_name' => $module,
        ]);
    }
}
