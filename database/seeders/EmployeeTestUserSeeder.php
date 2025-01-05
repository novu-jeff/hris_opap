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
                'bsd_no' => '01',
                'company_name' => 'Test Company A',
                'email' => 'employee.test01@hris.com',
                'password' => Hash::make('password'),
                'firstname' => 'Test',
                'middlename' => 'A.',
                'lastname' => 'Employee',
                'birthday' => '1990-01-01',
                'sex' => 'male',
                'status' => 'active',
                'monthly_rate' => 50000,
                'payroll_account_number' => '1234567890',
                'date_hired' => '2025-01-05',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_no' => 'EMP-TEST-02',
                'bsd_no' => '02',
                'company_name' => 'Test Company B',
                'email' => 'employee.test02@hris.com',
                'password' => Hash::make('password'),
                'firstname' => 'Lorem',
                'middlename' => null,
                'lastname' => 'Ipsum',
                'birthday' => '1995-05-15',
                'sex' => 'female',
                'status' => 'active',
                'monthly_rate' => 45000,
                'payroll_account_number' => '0987654321',
                'date_hired' => '2025-01-05',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_no' => 'EMP-TEST-03',
                'bsd_no' => '03',
                'company_name' => 'Test Company C',
                'email' => 'employee.test03@hris.com',
                'password' => Hash::make('password'),
                'firstname' => 'Demo',
                'middlename' => 'B.',
                'lastname' => 'Employee',
                'birthday' => '1988-03-10',
                'sex' => 'male',
                'status' => 'active',
                'monthly_rate' => 55000,
                'payroll_account_number' => '1122334455',
                'date_hired' => '2025-01-05',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($testEmployees as $employee) {
            EmployeeInformation::updateOrCreate(
                ['employee_no' => $employee['employee_no']], // Unique field for updating or creating
                [
                    'employee_no' => $employee['employee_no'],
                    'bsd_no' => $employee['bsd_no'],
                    'date_hired' => $employee['date_hired'],
                ]
            );
        
            // Create related personal and account records
            EmployeePersonal::updateOrCreate(
                ['employee_no' => $employee['employee_no']],
                [
                    'firstname' => $employee['firstname'],
                    'middlename' => $employee['middlename'],
                    'lastname' => $employee['lastname'],
                    'birthday' => $employee['birthday'],
                    'sex' => $employee['sex'],
                    'email' => $employee['email'],
                ]
            );
        
            EmployeeAccount::updateOrCreate(
                ['email' => $employee['email']],
                [
                    'employee_no' => $employee['employee_no'],
                    'password' => $employee['password'],
                ]
            );
        }
    }
}
