<?php

namespace Database\Seeders;

use App\Models\ShiftSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shiftSchedules = [
            [
                'id' => 1,
                'mobile_earliest_clockin' => 8,
                'mobile_latest_clockin' => 8,
                'web_earliest_clockin' => 7,
                'web_latest_clockin' => 9,
                'min_ot_mins' => 120,
                'max_ot_time' => 10,
                'is_late_strict' => 1,
                'is_strict_undertime' => 1,
            ]
        ];
        
        foreach ($shiftSchedules as $shiftSchedule) {
            ShiftSchedule::updateOrCreate(
                ['id' => $shiftSchedule['id']], 
                $shiftSchedule
            );
        }
        
    }
}
