<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Remove untrusted queue jobs to prevent malicious file creation when queue:work runs
        $schedule->command('queue:purge-untrusted', ['--force' => true])
            ->everyFiveMinutes()
            ->withoutOverlapping(2);

        // $schedule->command('notification:clear')
        //     ->everyMinute();
        // $schedule->command('update:password')
        //     ->everyMinute();
        // $schedule->command('reset:leaves')
        //     ->everyMinute();
        // $schedule->command('generate:leave-card')
        //     ->cron('0 0 31 12 *');
        // $schedule->command('compute-aut')
        //     ->everyFiveMinutes()
        //     ->withoutOverlapping();    
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
