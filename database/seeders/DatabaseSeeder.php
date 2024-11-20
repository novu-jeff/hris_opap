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
        $this->call([
            AdminAccountSeeder::class,
            RequirementSeeder::class,
            InterviewSeeder::class,
            JobPostSeeder::class,
            PositionSeeder::class,
            ViolationSeeder::class,
            SkillListSeeder::class,
            BranchSeeder::class,
            CostCenterSeeder::class,
            DepartmentSeeder::class,
            AnnouncementSeeder::class,
            JobCategoriesSeeder::class,
            LeaveTypesSeeder::class,
            OtherEarningsSeeder::class,
            OtherDeductionsSeeder::class
        ]);
    }
}
