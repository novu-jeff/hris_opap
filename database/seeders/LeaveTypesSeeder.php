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
            ['code' => 'VL', 'name' => 'Vacation Leave'],
            ['code' => 'SL', 'name' => 'Sick Leave'],
            ['code' => 'SPL', 'name' => 'Special Privilege Leave'],
            ['code' => 'ML', 'name' => 'Maternity Leave'],
            ['code' => 'PL', 'name' => 'Paternity Leave'],
            ['code' => 'SOLO', 'name' => 'Solo Parent Leave'],
        ];

        foreach ($leave_types as $leave_type) {
            LeaveType::updateOrCreate(
                ['code' => $leave_type['code'], 'name' => $leave_type['name']], 
                $leave_type
            );
        }
    }
}
