<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyTimeRecordService {
    /**
    * Retrieve the Daily Time Record for a specific employee and month.
    */
    public function getDailyTimeRecord($id, $date)
    {
        $errors = [];

        $date = Carbon::createFromFormat('F, Y', $date);

        Log::info('Showing DTR for Date: ' . $date);

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        # get the employee's information
        $employee = $this->getEmployee($id);

        $shift = $this->fetchData('shift_schedule', $employee->shift_id, $errors, 'Shift schedule is missing');
        $schedule = $this->fetchData('employee_schedules', $employee->schedule_id, $errors, 'Schedule is missing');
        $overtime = $this->fetchOvertime($id, $startDate, $endDate);
        $leaves = $this->fetchLeaves($id, $startDate, $endDate);
        $holidays = $this->fetchHolidays($startDate, $endDate);

        if (!$employee) {
            $errors[] = 'Employee not found.';
            throw new \Exception(implode("\n", $errors));
        }
        if (!$shift) {
            $errors[] = "Shift schedule is missing for employee {$id}. Please assign one.";
        }
        if (!$schedule) {
            $errors[] = "Employee schedule is missing for employee {$id}. Please assign one.";
        }

        # Throw all errors if any
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        # Fetch clock-in/out data
        $clockData = $this->getClockData($employee, $startDate, $endDate);

        # Generate all days in the month
        $allDays = $this->generateAllDays($startDate, $endDate);

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
    /**
    * Compute the daily time record based on clock data, schedules, and holidays.
    */
    public function computeDailyTimeRecord(
        object $employee, 
        object $shift, 
        object $schedule, 
        Object $clockData, 
        object $overtime, 
        int $leaves,
        object $holiday,
        $allDays)
    {
        # Initialize totals
        $totalOfWorkDaysForCurrentMonth  = 0;
        $totalPresentDays = 0;
        $totalRestDay = 0;
    
        # Get schedule days and converted holidays
        $scheduleDays = $this->getDaysSchedule($schedule);
        $convertedHolidays = $this->convertHolidayToArray($holiday);
    
        # Default required minutes to render
        $requiredMinsToRender = 480; 
    
        # Check if shift is flexible
        if ($shift->shift_duration === 'flexible') {
            $requiredMinsToRender = 480;
        }
    
        # Parse break times from shift data
        $breakOut = Carbon::parse($shift->break_out);
        $breakIn = Carbon::parse($shift->break_in);            
    
        # Calculate break time duration
        $breakTimeDuration = $breakOut->diffInMinutes($breakIn);

        # Map clock-in/out data to dates
        $mappedClockData = [];
        foreach ($allDays as $day) {
            $dayTextFormat = Carbon::parse($day)->format('l');
            $overtimeDuration = null;
    
            $remarks = [];
    
            # Find clock entry for the current day
            $clockEntry = $clockData->firstWhere(fn($item) => Carbon::parse($item->created_at)->isSameDay(Carbon::parse($day)));
            
            # Find overtime entry for the current day
            $overtimeEntry = $overtime->firstWhere(fn($item) => Carbon::parse($item->date)->isSameDay(Carbon::parse($day)));
    
            #get the origin of time logs
            $origin = $clockEntry ? $clockEntry->origin : 'biometrics';  #default biometrics  if null

            # Check if the employee was present
            $clockIn = $clockEntry ? $clockEntry->clock_in_am : null;
            $clockOut = $clockEntry ? $clockEntry->clock_out_pm : null;

            # Compute total minutes worked
            $totalMinutesConsumed = $this->calculateTotalMinutesWorked($clockIn, $clockOut, $shift, $breakTimeDuration, $origin, $remarks);

            # Calculate overtime
            $overtimeDuration = $overtimeEntry ? $totalMinutesConsumed - 480 : null;

           if (in_array($dayTextFormat, $scheduleDays)) { # Check if the day is part of the schedule
                $totalOfWorkDaysForCurrentMonth++;
                if (is_null($clockEntry)) {
                    $remarks[] = 'Absent';
                    Log::info("No clock entry found for $day, marking as Absent.");
                }
            } else if ($totalMinutesConsumed < $requiredMinsToRender && $totalMinutesConsumed  != 0) { 
                $remarks[] = 'Undertime';  # Cap total minutes at 480 and check for undertime
            } else {
                $totalRestDay++;
                $remarks[] = 'Rest day';
            }
    
            # Mark the day as a holiday if applicable
            if (in_array($day, $convertedHolidays['regular']) || in_array($day, $convertedHolidays['special']) || in_array($day, $convertedHolidays['company'])) {
                $remarks[] = 'Holiday';
            }
    
            # Check if the employee was present
            if ($clockEntry && $clockEntry->clock_in_am && $clockEntry->clock_out_pm) {
                $totalPresentDays++;
            }

            if($totalMinutesConsumed > $requiredMinsToRender)
            {
                $totalMinutesConsumed = $requiredMinsToRender;
            }
    
            # Add data for the current day
            $mappedClockData[] = [
                'date' => $day,
                'clock_in_am' => $clockIn,
                'clock_out_am' => $clockEntry ? $clockEntry->clock_out_am : null,
                'clock_in_pm' => $clockEntry ? $clockEntry->clock_in_pm : null,
                'clock_out_pm' => $clockOut,
                'origin' => $origin,
                'remarks' => $remarks ? $remarks : null,
                'overtime_approved' => $clockEntry ? $overtimeDuration : null,
                'total_mins_consumed' => $clockEntry ? $totalMinutesConsumed : null,
            ];
        }
    
        # Format the final result
        Log::info('Formatting DTR data for the employee.');
        $dtrNewFormat = [
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
                'special_holidays' => count($convertedHolidays['special']),
                'regular_holidays' => count($convertedHolidays['regular']),
                'total_days_work' => $totalOfWorkDaysForCurrentMonth,
            ]
        ];
    
        return $dtrNewFormat;
    }
    /**
    * Get total time
    */
    private function calculateTotalMinutesWorked($clockIn, $clockOut, $shift, $breakTimeDuration, $origin, &$remarks)
    {
        Log::info('Calculate Total Minutes');

        if ($clockIn && $clockOut) {
            $clock_start = Carbon::createFromFormat('h:i A', $clockIn);
            $clock_end = Carbon::createFromFormat('h:i A', $clockOut);

            $start_shift = Carbon::parse($shift->start_shift);

            switch ($origin) {
                case 'biometrics': 
                    $latest_in = Carbon::parse($shift->latest_in);
                    break;
                case 'mobile':
                    $latest_in = Carbon::parse($shift->mobile_latest_clockin);
                    break;
                case 'web':
                    $latest_in = Carbon::parse($shift->web_latest_clockin);
                    break;
            }
            
            if ($clock_start > $latest_in || $clock_start > $start_shift) {
                $remarks[] = 'late';
            } 
    
            $totalMinutesConsumed = $clock_start->diffInMinutes($clock_end) - $breakTimeDuration;
            Log::info('time in: ' . $clockIn);
            Log::info('Time Out' . $clock_end);
            Log::info('totalmins' . $totalMinutesConsumed);

            return $totalMinutesConsumed;
        }
    
        return 0;
    }   
    /**
    * Get the employee details.
    */
    public function getEmployee($id)
    {
          # Fetch employee account details
          return DB::table('employee_account')
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
    }
    /**
    * Get all the clock data of the employee based on the start and end date.
    */
    public function getClockData($employee, $startDate, $endDate)
    {
        return DB::table('employee_clock_in_out')
            ->select(
                'bsd_no',
                'employee_no',
                'clock_in_am',
                'clock_out_am',
                'clock_in_pm',
                'clock_out_pm',
                'created_at',
                'origin',
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) use ($employee) {
                $query->where('bsd_no', $employee->bsd_no)
                    ->orWhere('employee_no', $employee->employee_no);
            })
            ->get();
    }
     /**
     * Fetch data from the database.
     */
    private function fetchData($table, $id, &$errors, $errorMessage)
    {
        $this->logInfo("Fetching data from $table for ID: $id.");
        $data = DB::table($table)->where('id', $id)->first();
        if (!$data) {
            $errors[] = $errorMessage . " (ID: $id)";
        }
        return $data;
    }
    /**
    * Generate all days in the given date range.
    */
    private function generateAllDays($startDate, $endDate)
    {
        $this->logInfo("Generating all days for the month.");
        return collect($startDate->toPeriod($endDate))->map(fn($day) => $day->toDateString());
    }
    /**
    * Fetch overtime data.
    */
    private function fetchOvertime($id, $startDate, $endDate)
    {
        $this->logInfo("Fetching overtime for employee ID: $id between $startDate and $endDate.");
        return DB::table('employee_atro')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('employee_no', $id)
            ->get();
    }
    /**
    * Fetch leave data.
    */
    private function fetchLeaves($id, $startDate, $endDate)
    {
        $this->logInfo("Fetching leaves for employee ID: $id.");
        return DB::table('employee_leave')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('employee_no', $id)
            ->count();
    }
    /**
    * Fetch holiday data.
    */
    private function fetchHolidays($startDate, $endDate)
    {
        $this->logInfo("Fetching holidays between $startDate and $endDate.");
        return DB::table('holidays')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    }
    /**
    * Convert schedule to array.
    */
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
    /**
    * Convert Holiday into Array
    */
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
    private function logInfo($message)
    {
        Log::info($message);
    }

}