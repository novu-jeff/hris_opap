<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
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
            'payroll' => [],
            'ess' => [
                'leave',
                'obs',
                'arto',
                'request-log',
                'announcements',
                'employee-profile-approval',
                'messages',
                'faqs'
            ],
            'reports' => [
                'dtr'
            ],
            'settings' => [
                'company-information',
                'scheduler',
                'tranches',
                'branches',
                'departments',
                'sections',
                'assessments',
                'requirements',
                'users',
                'roles',
                'bank-information',
                'employment-type',
                'positions',
                'violations',
                'leave-types',
                'leave-credits',
                'gsis-billing',
                'employee-earnings',
                'employee-deductions',
                'other-earnings',
                'other-deductions',
                'shift-schedule',
                'employee-schedule',
                'holidays',
            ],
            'employee' => [
                'apply-leave',
                'clock-in-out',
                'remaining-credit',
                'apply-atro',
                'apply-request-timelog',
                'payslip',
                'employee-messages',
                'apply-obs',
                'employee-dtr',
                'my-directory',
                'my-team',
                'employee-announcements',
                'my-profile',
            ]
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                $guardName = $module === 'employee' ? 'employee' : 'web';

                // Check if the action should only be "read"
                if (in_array($action, ['my-directory', 'my-team', 'employee-announcements', 'payslip'])) {
                    // Only create "read" permission for these actions
                    $this->createPermission($module, "read $action", $guardName);
                } else {
                    // Create both "read" and "write" permissions for the rest
                    $this->createPermission($module, "read $action", $guardName);
                    $this->createPermission($module, "write $action", $guardName);
                }
            }
        }
    }

    private function createPermission(string $module, string $permissionName, string $guardName)
    {
        Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => $guardName,
            'module_name' => $module,
        ]);
    }
}
