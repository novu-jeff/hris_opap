<?php

namespace App\Services;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

use function PHPUnit\Framework\returnSelf;

class DailyTimeRecordService {

    // public function getDailyTimeRecord($id, $date)
    // {
    //     $date = Carbon::createFromFormat('F, Y', $date);

    //     Log::info('Showing DTR for Date: ' . $date);

    //     $startDate = $date->copy()->startOfMonth();
    //     $endDate = $date->copy()->endOfMonth();

    //     $employeeClockQuery = DB::table('employee_clock_in_out')
    //     ->whereBetween('created_at', [$startDate, $endDate])
    //     ->orWhereNull('created_at');
    
    //     // Main query
    //     $dtr = DB::table('employee_account')
    //         ->leftJoin('employee_information', 'employee_account.employee_no', '=', 'employee_information.employee_no')
    //         ->leftJoinSub($employeeClockQuery, 'clock_data', function ($join) {
    //             $join->on('employee_information.bsd_no', '=', 'clock_data.bsd_no')
    //                 ->orOn('employee_information.employee_no', '=', 'clock_data.employee_no');
    //         })
    //         ->leftJoin('employee_schedules', 'employee_information.schedule_id', '=', 'employee_schedules.id')
    //         ->leftJoin('shift_schedule', 'employee_information.shift_id', '=', 'shift_schedule.id')
    //         ->leftJoin('employee_personal', 'employee_account.employee_no', '=', 'employee_personal.employee_no')
    //         ->leftJoin('positions', 'employee_information.position_id', '=', 'positions.id')
    //         ->leftJoin('sections', 'employee_information.section_id', '=', 'sections.id')
    //         ->leftJoin('departments', 'sections.department_id', '=', 'departments.id')
    //         ->select(
    //             # Employee personal details
    //             'employee_account.employee_no',
    //             'employee_information.bsd_no',
    //             'employee_personal.firstname',
    //             'employee_personal.middlename',
    //             'employee_personal.lastname',

    //             # Clock in and out
    //             'clock_data.clock_in_am',
    //             'clock_data.clock_out_am',
    //             'clock_data.clock_in_pm',
    //             'clock_data.clock_out_pm',

    //             'clock_data.total_mins_consumed',
    //             'clock_data.created_at',
    //             'clock_data.origin',
    //             'clock_data.isLate',
    //             'clock_data.isUnderTime',
    //             'clock_data.isHalfDay',

    //             'clock_data.total_mins_consumed',
    //             'clock_data.mins_ot',
    //             'clock_data.overall_mins',

    //             # Employee Shift
                

    //             # Employee Additional Information
    //             'positions.code as position_code',
    //             'positions.name as position_name',
    //             'positions.salary',
    //             'departments.name as department_name',
    //             'departments.code as department_code'
    //         )
    //         ->where('employee_account.employee_no', $id)
    //         ->get();

    //     dd($dtr);
    //     $allDays = collect();
    //     foreach ($startDate->toPeriod($endDate) as $day) {
    //         $allDays->push($day->toDateString()); 
    //     }

    //     $mappedClockData = [];
    //     foreach ($allDays as $day) {

    //         $clockData = $dtr->firstWhere(function($item) use ($day) {
    //             return Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day));
    //         });

    //         $mappedClockData[] = [
    //             'date' => $day,
    //             'clock_in_am' => $clockData ? $clockData->clock_in_am : null,
    //             'clock_out_am' => $clockData ? $clockData->clock_out_am : null,
    //             'clock_in_pm' => $clockData ? $clockData->clock_in_pm : null,
    //             'clock_out_pm' => $clockData ? $clockData->clock_out_pm : null,
    //             'origin' => $clockData ? $clockData->origin : null,
    //             'total_mins_consumed' => $clockData ? $clockData->total_mins_consumed : null,
    //         ];
    //     }

    //     // Assign data to $this->dtr
    //     $dtr_new_format = [
    //         'employee_account' => [
    //             'employee_no' => $dtr->first()->employee_no,
    //             'firstname' => $dtr->first()->firstname,
    //             'lastname' => $dtr->first()->lastname,
    //             'middlename' => $dtr->first()->middlename ? strtoupper(substr($dtr->first()->middlename, 0, 1)) . '.' : '',
    //             'position' => $dtr->first()->position_name . ' (' . $dtr->first()->position_code . ')',
    //             'department' => $dtr->first()->department_name . ' (' . $dtr->first()->department_code . ')',
    //             'salary' => $dtr->first()->salary,
    //         ],
    //         'clock_in_out' => $mappedClockData
    //     ];
        



    //     return $dtr_new_format;
    // }

    public function getDailyTimeRecord($id, $date)
    {
        $errors = [];

        $date = Carbon::createFromFormat('F, Y', $date);

        Log::info('Showing DTR for Date: ' . $date);

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        # Fetch employee account details
        $employee = DB::table('employee_account')
            ->leftJoin('employee_information', 'employee_account.employee_no', '=', 'employee_information.employee_no') #Information
            ->leftJoin('employee_schedules', 'employee_information.schedule_id', '=', 'employee_schedules.id') #schedule
            ->leftJoin('shift_schedule', 'employee_information.shift_id', '=', 'shift_schedule.id') #shift
            ->leftJoin('employee_personal', 'employee_account.employee_no', '=', 'employee_personal.employee_no') #personal details
            ->leftJoin('positions', 'employee_information.position_id', '=', 'positions.id') # position
            ->leftJoin('sections', 'employee_information.section_id', '=', 'sections.id') #section
            ->leftJoin('departments', 'sections.department_id', '=', 'departments.id') #department
            ->select(
                'employee_account.employee_no',
                'employee_information.bsd_no',
                'employee_information.shift_id',
                'employee_information.schedule_id',
                
                'employee_personal.firstname',
                'employee_personal.middlename',
                'employee_personal.lastname',
                'positions.code as position_code',
                'positions.name as position_name',
                'positions.salary',
                'departments.name as department_name',
                'departments.code as department_code',
                'employee_schedules.*',
                'shift_schedule.*',
            )
            ->where('employee_account.employee_no', $id)
            ->first();
            
        if (!$employee) {
            $errors[] = 'Employee not found.';
            throw new \Exception(implode("\n", $errors));
        }
        
        # get the shift of employee
        $shift = DB::table('shift_schedule')
                ->where('id', $employee->shift_id)
                ->first();

        if (!$shift) {
            $errors[] = "Shift schedule is missing for employee {$id}. Please assign one.";
        }
        
        # get the schedule of employee
        $schedule = DB::table('employee_schedules')
                ->where('id', $employee->schedule_id)
                ->first();

        if (!$schedule) {
            $errors[] = "Employee schedule is missing for employee {$id}. Please assign one.";
        }

        # Throw all errors if any
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        # Fetch clock-in/out data
        $clockData = DB::table('employee_clock_in_out')
            ->select(
                'bsd_no',
                'employee_no',
                'clock_in_am',
                'clock_out_am',
                'clock_in_pm',
                'clock_out_pm',
                'total_mins_consumed',
                'created_at',
                'origin',
                'isLate',
                'isUnderTime',
                'isHalfDay',
                'mins_ot',
                'overall_mins'
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) use ($employee) {
                $query->where('bsd_no', $employee->bsd_no)
                    ->orWhere('employee_no', $employee->employee_no);
            })
            ->get();

        # Generate all days in the month
        $allDays = collect();
        foreach ($startDate->toPeriod($endDate) as $day) {
            $allDays->push($day->toDateString());
        }

        $dailyTimeRecord = $this->computeDailyTimeRecord($employee, $shift, $schedule, $clockData, $allDays);

        return $dailyTimeRecord;
    }

    public function computeDailyTimeRecord(object $employee, object $shift, object $schedule, Object $clockData, $allDays)
    {
       
         # Map clock-in/out data to the dates
         $mappedClockData = [];
         foreach ($allDays as $day) {
             $clockEntry = $clockData->firstWhere(function ($item) use ($day) {
                 return Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day));
             });
 
             $mappedClockData[] = [
                 'date' => $day,
                 'clock_in_am' => $clockEntry ? $clockEntry->clock_in_am : null,
                 'clock_out_am' => $clockEntry ? $clockEntry->clock_out_am : null,
                 'clock_in_pm' => $clockEntry ? $clockEntry->clock_in_pm : null,
                 'clock_out_pm' => $clockEntry ? $clockEntry->clock_out_pm : null,
                 'origin' => $clockEntry ? $clockEntry->origin : null,
                 'total_mins_consumed' => $clockEntry ? $clockEntry->total_mins_consumed : null,
             ];
         }

        # Format the result
        $dtr_new_format = [
            'employee_account' => [
                'employee_no' => $employee->employee_no,
                'firstname' => $employee->firstname,
                'lastname' => $employee->lastname,
                'middlename' => $employee->middlename 
                    ? strtoupper(substr($employee->middlename, 0, 1)) . '.' 
                    : '',
                'position' => $employee->position_name . ' (' . $employee->position_code . ')',
                'department' => $employee->department_name . ' (' . $employee->department_code . ')',
                'salary' => $employee->salary,
            ],
            'clock_in_out' => $mappedClockData
        ];

        return $dtr_new_format;

    }
}