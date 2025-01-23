<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $product = config('app.product');

        $this->call([
            EmploymentTypesSeeder::class,
            PositionSeeder::class,
            ViolationSeeder::class,
            SkillListSeeder::class,   
            LeaveTypesSeeder::class,
            CompanyBusinessTypeSeeder::class,
            ShiftScheduleSeeder::class,
            EmployeeScheduleSeeder::class,
            CompanyInformationSeeder::class,
            PermissionSeeder::class,
            RolesSeeder::class,
            AdminAccountSeeder::class,
            SchedulerDefaultSeeder::class
        ]);

        if($product == 'testing' || $product == 'novu') {
            $this->call([
                AnnouncementSeeder::class,
                RequirementSeeder::class,
                InterviewSeeder::class,
                JobPostSeeder::class,
                AnnouncementSeeder::class,
                ApplicantTestUserSeeder::class,
                EmployeeTestUserSeeder::class
            ]);
        }

        
        if($product == 'opap') {
            $this->call([
                BranchSeeder::class,
                DepartmentSeeder::class,
                SectionSeeder::class,
                OtherEarningsSeeder::class,
                OtherDeductionsSeeder::class,
            ]);
        }
    }
}
