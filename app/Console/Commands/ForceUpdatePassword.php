<?php

namespace App\Console\Commands;

use App\Models\EmployeeAccount;
use App\Models\Scheduler;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ForceUpdatePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee force update password after the scheduled timespan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = EmployeeAccount::where('isNew', false)
            ->where('isToUpdatePassword', false)
            ->get();
    
        $schedule = Scheduler::where('schedule_name', 'change_password')
            ->first();
            

       if($schedule) {

            $resetInterval = $schedule->interval;
            $now = Carbon::now();
            
            foreach ($users as $user) {
                $lastUpdated = Carbon::parse($user->last_password_updated);
                $hoursSinceUpdate = $lastUpdated->diffInHours($now);
                if ($hoursSinceUpdate >= $resetInterval) {
                    $user->update([
                        'isToUpdatePassword' => true,
                        'last_password_updated' => $now
                    ]);
                }
            }
       }
    }
}
