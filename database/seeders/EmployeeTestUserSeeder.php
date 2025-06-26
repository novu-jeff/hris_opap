<?php

namespace Database\Seeders;

use App\Models\EmployeeAccount;
use App\Models\EmployeeInformation;
use App\Models\EmployeePersonal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testEmployees = [
            [
                'employee_no' => 'EMP-TEST-01',
                'bsd_no' => '959',
                'shift_id' => 1,
                'schedule_id' => 1,
                'section_id' => 1,
                'position_id' => 1,
                'company_name' => '',
                'email_id' => 'employee.test01@hris.com',
                'email' => 'employee01@gmail.com',
                'password' => Hash::make('password'),
                'firstname' => 'Kim Anne',
                'middlename' => 'T.',
                'lastname' => 'Llemos',
                'birthday' => '1990-01-01',
                'sex' => 'male',
                'status' => 'active',
                'monthly_rate' => '293191',
                'payroll_account_number' => '1234567890',
                'date_hired' => '2025-01-05',
                'gsis_no' => '10000000001',
                'pagibig_no' => '10000000002',
                'philhealth_no' => '10000000003',
                'sss_no' => '10000000004',
                'tin_no' => '10000000005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_no' => 'EMP-TEST-02',
                'bsd_no' => '100',
                'shift_id' => 1,
                'schedule_id' => 1,
                'section_id' => 2,
                'position_id' => 1,
                'company_name' => '',
                'email_id' => 'employee.test02@hris.com',
                'email' => 'employee02@gmail.com',
                'password' => Hash::make('password'),
                'firstname' => 'Albert',
                'middlename' => 'R.',
                'lastname' => 'Yabut',
                'birthday' => '1990-01-01',
                'sex' => 'male',
                'status' => 'active',
                'monthly_rate' => '293191',
                'payroll_account_number' => '1234567890',
                'date_hired' => '2025-01-05',
                'gsis_no' => '20000000001',
                'pagibig_no' => '20000000002',
                'philhealth_no' => '20000000003',
                'sss_no' => '20000000004',
                'tin_no' => '20000000005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($testEmployees as $employee) {
            EmployeeInformation::updateOrCreate(
                ['employee_no' => $employee['employee_no']], 
                [
                    'employee_no' => $employee['employee_no'],
                    'bsd_no' => $employee['bsd_no'],
                    'date_hired' => $employee['date_hired'],
                    'employment_type_id' => 1,
                    'shift_id' => $employee['shift_id'],
                    'schedule_id' => $employee['schedule_id'],
                    'section_id' => $employee['section_id'],
                    'position_id' => $employee['position_id'],
                    'monthly_rate' => $employee['monthly_rate']
                ]
            );
        
            EmployeePersonal::updateOrCreate(
                ['employee_no' => $employee['employee_no']],
                [
                    'firstname' => $employee['firstname'],
                    'middlename' => $employee['middlename'],
                    'lastname' => $employee['lastname'],
                    'birthday' => $employee['birthday'],
                    'sex' => $employee['sex'],
                    'gsis_no' => $employee['gsis_no'],
                    'pagibig_no' => $employee['pagibig_no'],
                    'philhealth_no' => $employee['philhealth_no'],
                    'sss_no' => $employee['sss_no'],
                    'tin_no' => $employee['tin_no'],
                ]
            );
        
            $user = EmployeeAccount::updateOrCreate(
                ['email' => $employee['email']],
                [
                    'email_id' => $employee['email_id'],
                    'email' => $employee['email'],
                    'employee_no' => $employee['employee_no'],
                    'password' => $employee['password'],
                ]
            );
            
            $user->assignRole('employee');
        }
    }
}
