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
            SchedulerDefaultSeeder::class,
            AnnouncementSeeder::class,
            HolidaySeeder::class,
            FAQSeeder::class,
            RequirementSeeder::class,
            InterviewSeeder::class,
        ]);

        if($product == 'private') {
            $this->call([
                AnnouncementSeeder::class,
                ApplicantTestUserSeeder::class,
                EmployeeTestUserSeeder::class,
            ]);
        }

        
        if($product == 'government') {
            $this->call([
                BranchSeeder::class,
                DepartmentSeeder::class,
                SectionSeeder::class,
                OtherEarningsSeeder::class,
                OtherDeductionsSeeder::class,
                TimeEquivalentSeeder::class,
                JobPostSeeder::class,
                PositionSeeder::class,
                TrancheSeeder::class,
                EmployeeTestUserSeeder::class,
            ]);
        }
    }
}
