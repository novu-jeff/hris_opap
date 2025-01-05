<?php

namespace Database\Seeders;

use App\Models\EmployeeSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeSchedule = [
            [
                'name' => 'Default Schedule',
                'description' => 'Default schedule for OPAP employees',
                'monday' => 1,
                'monday_remarks' => null,
                'tuesday' => 1,
                'tuesday_remarks' => null,
                'wednesday' => 1,
                'wednesday_remarks' => null,
                'thursday' => 1,
                'thursday_remarks' => null,
                'friday' => 1,
                'friday_remarks' => null,
                'saturday' => 0,
                'saturday_remarks' => 'Rest Day',
                'sunday' => 0,
                'sunday_remarks' => 'Rest Day',
            ],
        ];
        

        foreach ($employeeSchedule as $schedule) {
            EmployeeSchedule::updateOrCreate(
                ['name' => $schedule['name']], 
                $schedule
            );
        }
    }
}
