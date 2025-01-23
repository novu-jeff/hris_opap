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
        $leave_types = [
            ['code' => 'VL', 'name' => 'Vacation Leave', 'credits' => 15, 'isCummulative' => true],
            ['code' => 'SL', 'name' => 'Sick Leave' , 'credits' => 15, 'isCummulative' => true],
            ['code' => 'SPL', 'name' => 'Special Privilege Leave' , 'credits' => 3, 'isCummulative' => false],
            ['code' => 'ML', 'name' => 'Maternity Leave' , 'credits' => 105, 'isCummulative' => false],
            ['code' => 'PL', 'name' => 'Paternity Leave' , 'credits' => 7, 'isCummulative' => false],
            ['code' => 'SOLO', 'name' => 'Solo Parent Leave' , 'credits' => 7, 'isCummulative' => false],
        ];

        foreach ($leave_types as $leave_type) {
            LeaveType::updateOrCreate(
                ['code' => $leave_type['code'], 'name' => $leave_type['name']], 
                $leave_type
            );
        }
    }
}
