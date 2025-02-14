<?php

namespace App\Services;

use App\Models\EmployeeAUT;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyTimeRecordService {
    /**
    * Retrieve the Daily Time Record for a specific employee and month.
    */
    public function getDailyTimeRecord($employee_no, $coverageDate)
    {
        $errors = [];

        # get the employee's information
        $employee = $this->getEmployee($employee_no);

        $shift = $this->fetchData('shift_schedule', $employee->shift_id, $errors, 'Shift schedule is missing');
        $schedule = $this->fetchData('employee_schedules', $employee->schedule_id, $errors, 'Schedule is missing');
        $overtime = $this->fetchOvertime($employee_no, $coverageDate);
        $leaves = $this->fetchLeaves($employee_no, $coverageDate);
        $holidays = $this->fetchHolidays($coverageDate);

        if (!$employee) {
            $errors[] = 'Employee not found.';
            throw new \Exception(implode("\n", $errors));
        }
        if (!$shift) {
            $errors[] = "Shift schedule is missing for employee {$employee_no}. Please assign one.";
        }
        if (!$schedule) {
            $errors[] = "Employee schedule is missing for employee {$employee_no}. Please assign one.";
        }

        # Throw all errors if any
        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        # Fetch clock-in/out data
        $clockData = $this->getClockData($employee, $coverageDate);
        $employeeAut = $this->getEmployeeAut($employee, $coverageDate);

        # Generate all days in the month
        $allDays = $this->generateAllDays($coverageDate);

        # DTR Computation
        $dailyTimeRecord = $this->computeDailyTimeRecord(
                    $employee, 
                    $shift, 
                    $schedule, 
                    $clockData, 
                    $employeeAut,
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
        Object $employeeAut, 
        object $overtime, 
        int $leaves,
        object $holiday,
        $allDays) {
            
        # Initialize totals
        $totalOfWorkDaysForCurrentMonth  = 0;
        $totalPresentDays = 0;
        $totalRestDay = 0;
        $totalLateDuration = 0;
        $totalUndertimeDuration = 0;
        $totalOvertimeDuration = 0;
    
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

        $clockData = $clockData->toArray();
        $employeeAut = $employeeAut->toArray();
        $allDays = $allDays->toArray();

        foreach ($allDays as $day) {
            $dayTextFormat = Carbon::parse($day)->format('l');
            $overtimeDuration = null;
            $remarks = [];
        
            # Ensure $clockData is an array before using it
            $clockEntry = $clockData[$day] ?? [];
            $employeeAutEntry = $employeeAut[$day] ?? [];

            $absent = !empty($employeeAutEntry) ? $employeeAutEntry[0] : 0;
            $undertime = !empty($employeeAutEntry) ? $employeeAutEntry[1] : 0;
            $late = !empty($employeeAutEntry) ? $employeeAutEntry[2] : 0;

            # Ensure $overtime is an array before using it
            $overtimeEntry = is_array($overtime) ? collect($overtime)->firstWhere(fn($item) => Carbon::parse($item->date)->isSameDay(Carbon::parse($day))) : null;
        
            # Get origin of time logs
            $origin = !empty($clockEntry) ? 'biometrics' : 'web';
        
            # Get clock-in and clock-out times
            $clockIn = !empty($clockEntry) ? ($clockEntry[0] ?? null) : null;
            $clockOut = !empty($clockEntry) && count($clockEntry) > 1 ? end($clockEntry) : null;
        
            # Compute total minutes worked
            $totalMinutesConsumed = $this->calculateTotalMinutesWorked($clockIn, $clockOut, $shift, $breakTimeDuration, $origin, $remarks, $late);
        
            # Calculate overtime
            $overtimeDuration = $overtimeEntry ? max($totalMinutesConsumed - 480, 0) : null;

            $totalOvertimeDuration += $overtimeDuration;
            
            if (in_array($dayTextFormat, $scheduleDays)) { 
                $totalOfWorkDaysForCurrentMonth++;
                if (empty($clockEntry)) {
                    $remarks[] = 'Absent';
                }
            } elseif ($undertime >  0) {
                $remarks[] = 'Undertime';
            } else {
                $totalRestDay++;
                $remarks[] = 'Rest day';
            }
        
            # Mark as holiday if applicable
            if (in_array($day, $convertedHolidays['regular']) || in_array($day, $convertedHolidays['special']) || in_array($day, $convertedHolidays['company'])) {
                $remarks[] = 'Holiday';
            }
        
            # Check if employee was present
            if (!empty($clockEntry)) {
                $totalPresentDays++;
            }
        
            if ($totalMinutesConsumed > $requiredMinsToRender) {
                $totalMinutesConsumed = $requiredMinsToRender;
            }

            # Total of AUT per row
            $totalAutPerRow = $absent + $undertime + $late;
            $totalUndertimeDuration += $undertime;
            $totalLateDuration += $late;
        
            # Add data for the current day
            $mappedClockData[] = [
                'date' => $day,
                'clock_in' => $clockIn,
                'break_out' => !empty($clockEntry) && count($clockEntry) > 1 ? $clockEntry[1] : null,
                'break_in' => !empty($clockEntry) && count($clockEntry) > 2 ? $clockEntry[2] : null,
                'clock_out' => $clockOut,
                'total_aut' => $totalAutPerRow,
                'origin' => $origin,
                'absent' => $absent,
                'late' => $late,
                'undertime' => $undertime,
                'remarks' => !empty($remarks) ? $remarks : null,
                'overtime_approved' => !empty($clockEntry) ? $overtimeDuration : null,
                'total_mins_consumed' => !empty($clockEntry) ? $totalMinutesConsumed : null,
            ];
        }
                    
        # Format the final result
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
                'overtime' => $totalOvertimeDuration,
                'leaves' => $leaves,
                'rest_days' => $totalRestDay,
                'tota_late' => $totalLateDuration,
                'total_undertime' => $totalUndertimeDuration,
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
    private function calculateTotalMinutesWorked($clockIn, $clockOut, $shift, $breakTimeDuration, $origin, &$remarks, &$late)
    {

        Log::info('Calculate Total Minutes');

        if ($clockIn && $clockOut) {

            $clock_start = Carbon::createFromFormat('H:i:s', $clockIn);
            $clock_end = Carbon::createFromFormat('H:i:s', $clockOut);
            
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
            
            if ($clock_start > $latest_in || $clock_start > $start_shift || $late > 0) {
                $remarks[] = 'late';
            }     

            $totalMinutesConsumed = $clock_start->diffInMinutes($clock_end) - $breakTimeDuration;

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
    
    public function getClockData($employee, $coverageDate)  
    {
        
        $month = Carbon::parse($coverageDate)->format('m'); // Get selected month
        $year = Carbon::parse($coverageDate)->format('Y'); // Get selected year
    
        // Fetch logs within the given month & year
        $logs = EmployeeTimelogs::where('bsd_no', $employee->bsd_no)
            ->whereRaw("STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i') IS NOT NULL")
            ->whereRaw("MONTH(STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i')) = ?", [$month])
            ->whereRaw("YEAR(STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i')) = ?", [$year])
            ->orderByRaw("STR_TO_DATE(logdatetime, '%d/%m/%Y %H:%i')") // Order by date
            ->limit(100)
            ->get();
    
        // Initialize a collection
        $formattedData = collect();
    
        foreach ($logs as $log) {
            // Ensure correct datetime parsing
            $dateTime = Carbon::createFromFormat('d/m/Y H:i', $log->logdatetime);
            $date = $dateTime->format('Y-m-d'); // Format as YYYY-MM-DD
            $time = $dateTime->format('H:i:s'); // Format as HH:MM:SS
    
            // Ensure key exists before pushing
            if (!$formattedData->has($date)) {
                $formattedData[$date] = collect();
            }
    
            $formattedData[$date]->push($time);
        }
        // dd($formattedData);
        return $formattedData;
    }

    public function getEmployeeAut($employee, $coverageDate)
    {
        $month = Carbon::parse($coverageDate)->format('m'); # Get selected month
        $year = Carbon::parse($coverageDate)->format('Y'); # Get selected year

        // Fetch logs within the given month & year
        $logs = EmployeeAUT::where('bsd_no', $employee->bsd_no)
            ->whereRaw("MONTH(STR_TO_DATE(date, '%d/%m/%Y')) = ?", [$month])
            ->whereRaw("YEAR(STR_TO_DATE(date, '%d/%m/%Y')) = ?", [$year])
            ->orderByRaw("STR_TO_DATE(date, '%d/%m/%Y')") // Order by date
            ->limit(100)
            ->get();

        // Initialize a collection
        $formattedData = collect();
    
        foreach ($logs as $log) {
            // Ensure correct datetime parsing
            $dateTime = Carbon::createFromFormat('d/m/Y', $log->date);
            $date = $dateTime->format('Y-m-d'); // Format as DD-MM-YYYY
    
            // Ensure key exists before pushing
            if (!$formattedData->has($date)) {
                $formattedData[$date] = collect();
            }
    
            $formattedData[$date]->push($log->absences);
            $formattedData[$date]->push($log->undertime);
            $formattedData[$date]->push($log->lates);
        }

        // dd($formattedData);
        return $formattedData;

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
    private function generateAllDays($date)
    {

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        return collect($startDate->toPeriod($endDate))->map(fn($day) => $day->toDateString());
    }
    /**
    * Fetch overtime data.
    */
    private function fetchOvertime($id, $date)
    {

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        return DB::table('employee_atro')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('employee_no', $id)
            ->get();
    }
    /**
    * Fetch leave data.
    */
    private function fetchLeaves($id, $date)
    {

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $this->logInfo("Fetching leaves for employee ID: $id.");
        return DB::table('employee_leave')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('employee_no', $id)
            ->count();
    }
    /**
    * Fetch holiday data.
    */
    private function fetchHolidays($date)
    {

        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

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