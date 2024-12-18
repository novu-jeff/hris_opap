<?php

namespace App\Services;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

use function PHPUnit\Framework\returnSelf;

class DailyTimeRecordService {

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

        # get the overtime of employee
        $overtime = DB::table('employee_atro')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('employee_no', $id)
                ->where('status', 'approve')
                ->count();

        # get the leaves of employee
        $leaves = DB::table('employee_leave')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('employee_no', $id)
                ->where('status', 'approve')
                ->count();

        # get Holidays of employee
        $holidays = DB::table('holidays')
                ->whereBetween('date', [$startDate, $endDate])
                ->where('isActive', true)
                ->get();

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

        # DTR Computation
        $dailyTimeRecord = $this->computeDailyTimeRecord(
                    $employee, 
                    $shift, 
                    $schedule, 
                    $clockData, 
                    $overtime,
                    $leaves,
                    $holidays,
                    $allDays
                );

        return $dailyTimeRecord;
    }

    public function computeDailyTimeRecord(
            object $employee, 
            object $shift, 
            object $schedule, 
            Object $clockData, 
            int $overtime, 
            int $leaves,
            object $holiday,
            $allDays)
    {
        $totalOfWorkDaysForCurrentMonth  = 0;
        $totalPresentDays = 0;
        $totalLates = 0;
        $totalUndertime = 0;
        $totalHalfDay = 0;
        $totalRestDay = 0;

        $remarks = '';

        $scheduleDays = $this->getDaysSchedule($schedule);
        $convertedHolidays = $this->convertHolidayToArray($holiday);

        # Map clock-in/out data to the dates
        $mappedClockData = [];
        foreach ($allDays as $day) {
            $dayTextFormat = Carbon::parse($day)->format('l');

            $clockEntry = $clockData->firstWhere(function ($item) use ($day) {
                return Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day));
            });
            
            # check if the day is in the schedule
            if(in_array($dayTextFormat, $scheduleDays))
            {
                $totalOfWorkDaysForCurrentMonth++;
                $remarks = '';
            }  else {
                $totalRestDay++;
                $remarks = 'Rest day';
            }

            if(in_array($day, $convertedHolidays['regular']) || in_array($day, $convertedHolidays['special']) || in_array($day, $convertedHolidays['company'])) {
                $remarks = 'Holiday';
            }

            # Check if the employee was present (i.e., clocked in and out)
            if ($clockEntry && $clockEntry->clock_in_am && $clockEntry->clock_out_pm) {
                $totalPresentDays++;
            }

            # other Totals
            if ($clockEntry) {
                $totalLates += $clockEntry->isLate ? 1 : 0;
                $totalUndertime += $clockEntry->isUnderTime ? 1 : 0;
                $totalHalfDay += $clockEntry->isHalfDay ? 1 : 0;
            }

            $mappedClockData[] = [
                'date' => $day,
                'clock_in_am' => $clockEntry ? $clockEntry->clock_in_am : null,
                'clock_out_am' => $clockEntry ? $clockEntry->clock_out_am : null,
                'clock_in_pm' => $clockEntry ? $clockEntry->clock_in_pm : null,
                'clock_out_pm' => $clockEntry ? $clockEntry->clock_out_pm : null,
                'origin' => $clockEntry ? $clockEntry->origin : null,
                'remarks' => $remarks ? $remarks : null,
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
            'clock_in_out' => $mappedClockData,
            'summary' => [
                'days_works' => $totalPresentDays,
                'absences' => $totalOfWorkDaysForCurrentMonth - $totalPresentDays,
                'overtime' => $overtime,
                'leaves' => $leaves,
                'rest_days' => $totalRestDay,
                'lates' => $totalLates,
                'undertime' => $totalUndertime,
                'halfday' => $totalHalfDay,
                'special_holidays' => count($convertedHolidays['special']),
                'regular_holidays' => count($convertedHolidays['regular']),
                'total_days_work' => $totalOfWorkDaysForCurrentMonth,
            ]
        ];

        return $dtr_new_format;

    }

    public function getDaysSchedule(object $schedule)
    {
        $workDays = [];
        if ($schedule->monday) {
            $workDays[] = 'Monday';
        }
        if ($schedule->tuesday) {
            $workDays[] = 'Tuesday';
        }
        if ($schedule->wednesday) {
            $workDays[] = 'Wednesday';
        }
        if ($schedule->thursday) {
            $workDays[] = 'Thursday';
        }
        if ($schedule->friday) {
            $workDays[] = 'Friday';
        }
        if ($schedule->saturday) {
            $workDays[] = 'Saturday';
        }
        if ($schedule->sunday) {
            $workDays[] = 'Sunday';
        }
        return $workDays;
    }

    public function convertHolidayToArray(object $holidays)
    {
        $specialHoliday = [];
        $national = [];
        $companyHoliday = [];

        foreach($holidays as $hday)
        {
            if($hday->type === 'special')
            {
                $specialHoliday[] = $hday->date;
            } else if ($hday->type === 'national') {
                $national[] = $hday->date;
            } else {
                $companyHoliday[] = $hday->date;
            }
        }
            
        return [
            'special' => $specialHoliday,
            'regular' => $national,
            'company' => $companyHoliday,
        ];
    }

}