<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = config('app.product');

        if($product == 'government') {
            $leave_types = [
                ['code' => 'VL', 'name' => 'Vacation Leave', 'credits' => 15, 'isCummulative' => true],
                ['code' => 'SL', 'name' => 'Sick Leave', 'credits' => 15, 'isCummulative' => true],
                ['code' => 'MFL', 'name' => 'Mandatory/Forced Leave', 'credits' => 0, 'isCummulative' => false],
                ['code' => 'ML', 'name' => 'Maternity Leave', 'credits' => 105, 'isCummulative' => false],
                ['code' => 'PL', 'name' => 'Paternity Leave', 'credits' => 7, 'isCummulative' => false],
                ['code' => 'SPL', 'name' => 'Special Privilege Leave', 'credits' => 3, 'isCummulative' => false],
                ['code' => 'SOLO', 'name' => 'Solo Parent Leave', 'credits' => 7, 'isCummulative' => false],
                ['code' => 'STL', 'name' => 'Study Leave', 'credits' => 0, 'isCummulative' => false],
                ['code' => 'VAWC', 'name' => '10-Day VAWC Leave', 'credits' => 10, 'isCummulative' => false],
                ['code' => 'RP', 'name' => 'Rehabilitation Privilege', 'credits' => 180, 'isCummulative' => false],
                ['code' => 'SLBW', 'name' => 'Special Leave Benefits for Women', 'credits' => 60, 'isCummulative' => false],
                ['code' => 'SEL', 'name' => 'Special Emergency (Calamity) Leave', 'credits' => 5, 'isCummulative' => false],
                ['code' => 'AL', 'name' => 'Adoption Leave', 'credits' => 7, 'isCummulative' => false]
            ];
        } else {
            $leave_types = [
                ['code' => 'VL', 'name' => 'Vacation Leave', 'credits' => 15, 'isCummulative' => true],
                ['code' => 'SL', 'name' => 'Sick Leave', 'credits' => 15, 'isCummulative' => true],
            ];
        }

        foreach ($leave_types as $leave_type) {
            LeaveType::updateOrCreate(
                ['code' => $leave_type['code'], 'name' => $leave_type['name']], 
                $leave_type
            );
        }

    }
}
