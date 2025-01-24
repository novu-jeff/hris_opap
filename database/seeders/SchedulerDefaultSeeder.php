<?php

namespace Database\Seeders;

use App\Models\Scheduler;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchedulerDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'schedule_name' => 'clear_notification',
                'interval' => '1', 
                'latest_activity' => Carbon::now(),
            ],
            [
                'schedule_name' => 'change_password',
                'interval' => '2160', 
                'latest_activity' => Carbon::now(),
            ],
            [
                'schedule_name' => 'reset_leave_credits',
                'interval' => '52560', 
                'latest_activity' => Carbon::now(),
            ],
        ];

        foreach ($settings as $setting) {
            Scheduler::updateOrCreate(
                ['schedule_name' => $setting['schedule_name']], 
                $setting
            );
        }
    }
}
