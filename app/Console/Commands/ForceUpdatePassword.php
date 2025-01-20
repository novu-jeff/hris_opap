<?php

namespace App\Console\Commands;

use App\Models\EmployeeAccount;
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
    protected $description = 'Employee force update password after a month.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = EmployeeAccount::where('isNew', false)
            ->where('isToUpdatePassword', false)
            ->get();
    
        $reset = 30;

        foreach($users as $user) {
            $lastPasswordUpdated = \Carbon\Carbon::parse($user->last_password_updated);
            $now = \Carbon\Carbon::now();
            $diff = $now->diffInDays($lastPasswordUpdated);
            if($diff >= $reset) {
                $user->isToUpdatePassword = true;
                $user->last_password_updated = $now;
                $user->save();
            }
        }
    }
}
