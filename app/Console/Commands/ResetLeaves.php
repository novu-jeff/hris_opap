<?php

namespace App\Console\Commands;

use App\Models\EmployeeInformation;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\Scheduler;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class resetLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee leave credits will be reset after the scheduled timespan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
     
        $product = config('app.product');
        $leaveDefaultCredits = LeaveType::all();
        
        $schedule = Scheduler::where('schedule_name', 'reset_leave_credits')
            ->first();

        if($schedule) {

            $savedTimestamp = $schedule->latest_activity;
            $resetInterval = $schedule->interval;

            $afterInterval = Carbon::parse($savedTimestamp)->addHours($resetInterval);
            $now = Carbon::now();

            if ($product == 'opap') {
                if ($now >= $afterInterval) {
                    $users = EmployeeInformation::with('personal')->where('employment_type_id', 1)
                        ->get();
            
                    foreach ($users as $user) {
                        foreach ($leaveDefaultCredits as $leaveCredit) {
                            // Get existing leave credits for this user and leave type
                            $existingLeave = LeaveCredits::where('employee_no', $user->employee_no)
                                ->where('leave_type_id', $leaveCredit->id)
                                ->first();
            
                            $credits = 0;
            
                            if ($existingLeave && $existingLeave->credits != 0) {
                                // Apply gender-specific logic for different leave types
                                if ($leaveCredit->code == 'ML' && $user->personal->sex == 'female') {
                                    $credits = $existingLeave->credits;
                                } elseif ($leaveCredit->code == 'PL' && $user->personal->sex == 'male') {
                                    $credits = $existingLeave->credits;
                                } elseif ($leaveCredit->code == 'SOLO' || $leaveCredit->code == 'SPL') {
                                    $credits = 0;
                                } elseif ($leaveCredit->code !== 'PL' && $leaveCredit->code !== 'ML') {
                                    $credits = $existingLeave->credits;
                                }
                            } else {
                                // No existing leave, apply initial credits based on leave type and gender
                                if ($leaveCredit->code == 'ML' && $user->personal->sex == 'female') {
                                    $credits = $leaveCredit->credits;
                                } elseif ($leaveCredit->code == 'PL' && $user->personal->sex == 'male') {
                                    $credits = $leaveCredit->credits;
                                } elseif ($leaveCredit->code == 'SOLO' || $leaveCredit->code == 'SPL') {
                                    $credits = 0;
                                } elseif ($leaveCredit->code !== 'PL' && $leaveCredit->code !== 'ML') {
                                    $credits = $leaveCredit->credits;
                                }
                            }
            
                            if ($leaveCredit->isCummulative && $existingLeave) {
                                $credits = $existingLeave->credits + $leaveCredit->credits;
                            }
            
                            // Update or create the leave credits record
                            LeaveCredits::updateOrCreate(
                                [
                                    'employee_no' => $user->employee_no,
                                    'leave_type_id' => $leaveCredit->id,
                                ],
                                [
                                    'credits' => $credits
                                ]
                            );
                        }
                    }
                }
            }
            

        }

    }
}
