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

            CompanyBusinessTypeSeeder::class,
            CompanyInformationSeeder::class,
            PermissionSeeder::class,
            RolesSeeder::class,
            AdminAccountSeeder::class,

            RequirementSeeder::class,
            InterviewSeeder::class,
            FAQSeeder::class,
            SkillListSeeder::class,   

            EmploymentTypesSeeder::class,

            ViolationSeeder::class,
            LeaveTypesSeeder::class,
            ShiftScheduleSeeder::class,
            EmployeeScheduleSeeder::class,
            SchedulerDefaultSeeder::class,
            AnnouncementSeeder::class,
            HolidaySeeder::class,
            
        ]);

        if(!$isExternalTimelogs) {

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
