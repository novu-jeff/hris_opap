<?php

namespace Database\Seeders;

use App\Models\ShiftSchedule;
use Carbon\Carbon;
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
                'name' => 'Default Shift',
                'description' => 'Default shift for OPAPRU - flexible 8 hours',
                'shift_duration' => 'flexible',
                'earliest_in' => '07:00',
                'latest_in' => '09:00',
                'start_shift' => null,
                'break_out' => '12:00',
                'break_in' => '13:00',
                'end_shift' => null,
                'work_setup' => 'hybrid',
                'min_ot_mins' => 120,
                'max_ot_time' => '22:00',
                'mobile_earliest_clockin' => '08:00',
                'mobile_latest_clockin' => '08:00',
                'web_earliest_clockin' => '07:00',
                'web_latest_clockin' => '09:00',
            ],
        ];

        foreach ($shiftSchedules as $shiftSchedule) {
            ShiftSchedule::updateOrCreate(
                ['name' => $shiftSchedule['name']], // Using 'name' as a unique identifier to update or create
                $shiftSchedule
            );
        }
    }
}
