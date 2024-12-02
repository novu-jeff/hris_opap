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

        $product = env('APP_PRODUCT');

        $this->call([
            AdminAccountSeeder::class,
            RequirementSeeder::class,
            InterviewSeeder::class,
            JobPostSeeder::class,
            PositionSeeder::class,
            ViolationSeeder::class,
            SkillListSeeder::class,   
            AnnouncementSeeder::class,
            JobCategoriesSeeder::class,
            LeaveTypesSeeder::class,
        ]);

        if($product == 'opap') {
            $this->call([
                BranchSeeder::class,
                DepartmentSeeder::class,
                SectionSeeder::class,
                OtherEarningsSeeder::class,
                OtherDeductionsSeeder::class
            ]);
        }
    }
}
