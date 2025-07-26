<?php

namespace App\Services;

use App\Models\EmployeeAUT;
use App\Models\EmployeeTimelogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeePersonal;

class DailyTimeRecordService {
    protected $bsd_emp_identical;

    public function __construct()
    {
        $this->bsd_emp_identical = config('app.bsd_emp_identical');
    }
    /**
    * Retrieve the Daily Time Record for a specific employee and date.
    * Date could be month-year or date range.
    */
    public function getDailyTimeRecord($employee_no, $dateInput)
    {
        try {
            # Case 1: Date Range Input (array with 2 elements)
            if (is_array($dateInput) && count($dateInput) === 2) {
                $startDate = Carbon::parse($dateInput[0])->startOfDay()->toDateTimeString();
                $endDate = Carbon::parse($dateInput[1])->endOfDay()->toDateTimeString();
            }
            # Case 2: Month-Year Input (e.g. "07-2025")
            elseif (is_string($dateInput)) {
                $monthCarbon = Carbon::createFromFormat('m-Y', $dateInput);
                $startDate = $monthCarbon->startOfMonth()->toDateTimeString();
                $endDate = $monthCarbon->endOfMonth()->endOfDay()->toDateTimeString();
            } else {
                abort(400, 'Invalid date format. Provide MM-YYYY or an array with two dates.');
            }
        } catch (\Exception $e) {
            abort(400, 'Invalid date input. ' . $e->getMessage());
        }

        # get bsd number
        $bsd_no = $this->bsd_emp_identical ? $employee_no : $this->getBsdNo($employee_no);

        $logs = EmployeeTimelogs::where('employee_id', $bsd_no)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();

        $employee = EmployeePersonal::where('employee_no', $employee_no)
            ->first()
            ->toArray() ?? [];

        $logs = $this->processLogs($employee, $logs, $dateInput, true);
        $dtr = $this->computeDTR($employee_no, $logs, $dateInput);

        return [
            'logs' => $dtr['formated_logs'],
            'summary' => $dtr['summary'],
        ];
    }
    
    public function getLogs(?string $timestamp = null, ?string $employee_id = null)
    {
        $logs = EmployeeTimelogs::with('employee.personal')
            ->when($employee_id, fn($q) => $q->where('employee_id', $employee_id))
            ->when($timestamp, fn($q) => $q->where('timestamp', 'like', "{$timestamp}%"))
            ->orderBy('timestamp')
            ->get();

        return $this->processLogs($logs, $timestamp);
    }

    # Main
    private function computeDTR($employee_no, $logs, $dateInput) 
    {
        try {
            # Parse date input to get start and end date
            if (is_array($dateInput) && count($dateInput) === 2) {
                $startDate = Carbon::parse($dateInput[0])->startOfDay();
                $endDate = Carbon::parse($dateInput[1])->endOfDay();
            } elseif (is_string($dateInput)) {
                # Handle MM-YYYY format
                $monthCarbon = Carbon::createFromFormat('m-Y', $dateInput);
                $startDate = $monthCarbon->copy()->startOfMonth();
                $endDate = $monthCarbon->copy()->endOfMonth();
            } else {
                # Invalid date input
                abort(400, 'Invalid date input. Provide MM-YYYY or an array with two dates.');
            }
        } catch (\Exception $e) {
            # Catch parsing errors
            abort(400, 'Invalid date input format. ' . $e->getMessage());
        }

        # Get today's date
        $today = Carbon::today();
        $formattedLogs = [];

        $weeklySchedule = $this->getWeeklySchedule($employee_no);
        $employeeSchedule = $this->getShiftSchedule($employee_no);
        $is_break_required = $employeeSchedule->is_breaktime_required;

        # Counters
        $absences = 0;
        $workedDays = 0;
        $restDays = 0;
        $legalHolidays = 0;
        $specialHolidays = 0;
        $workedOnLegalHolidays = 0;
        $workedOnSpecialHolidays = 0;

        $total_tardiness_perminutes = 0;
        $total_tardiness_freq = 0;
        $total_undertime_minutes = 0;
        $total_undertime_freq = 0;
        $total_overtime_perminutes = 0;
        $total_overtime_freq = 0;

        $leavesCount = 0;

        $leaves = $this->getTotalLeaves($employee_no, $dateInput);
        $leavesCount += $leaves['count'];
        $leavesCollection = collect($leaves['dates']);

        $overtime = $this->getTotalOvertime($employee_no, $dateInput);

        $total_overtime_perminutes = $overtime['raw_minutes'];
        $total_overtime_freq = $overtime['count'];

        $overtimeCollection = collect($overtime['dates']);

        for ($date = $startDate->copy(); $date <= $endDate->copy(); $date->addDay()){
            $dateString = $date->toDateString();
            $isFuture = $date->gt($today);
            $remarks = [];
            $isLeave = false;

            $dateLogs = $logs[$dateString] ?? null;

            $date_is_in_logs = isset($logs[$dateString]) && !empty($logs[$dateString]);

            $dayName = strtolower(Carbon::parse($dateString)->format('l'));

            #overtime 
            $matchLeave = $leavesCollection->first(function ($leave) use ($dateString) {
                return $leave->date === $dateString;
            });

            if($matchLeave){
                $isLeave  = true;
            }

            $checkAttendance = $this->checkAttendance($dateString,$date_is_in_logs,$weeklySchedule, $dayName, $isFuture, $isLeave);
            
            if ($checkAttendance['isAbsent']) $absences++;
            if ($checkAttendance['isWorkedDays']) $workedDays++;
            if ($checkAttendance['isRestDays']) $restDays++;
            if ($checkAttendance['isLegalHolidays']) $legalHolidays++;
            if ($checkAttendance['isSpecialHolidays']) $specialHolidays++;
            if ($checkAttendance['isWorkedOnLegalHolidays']) $workedOnLegalHolidays++;
            if ($checkAttendance['isWorkedOnSpecialHolidays']) $workedOnSpecialHolidays++;

            $remarks = array_merge($remarks, $checkAttendance['remarks']);
            
            # leaves
            foreach ($leaves['dates'] as $leaveDate) {

                if($dateString  == $leaveDate->date) {
                    $remarks[] = 'Leave';
                }

            }

            #overtime 
            $matchedOvertime = $overtimeCollection->first(function ($ot) use ($dateString) {
                return $ot->date === $dateString;
            });

            if (isset($logs[$dateString])) {
                $formattedLogs[$dateString] = $logs[$dateString];

                # aut 
                $aut = $this->undertimeAndTardiness($employee_no, $employeeSchedule, $dateLogs,$dateString);

                # Assign the correct values to formatted logs
                $formattedLogs[$dateString]['aut']['tardiness']['minutes'] = $aut['tardiness_minutes'];
                $formattedLogs[$dateString]['aut']['undertime']['minutes'] = $aut['undertime_minutes'];

                # Accumulate totals
                $total_tardiness_perminutes += $aut['tardiness_minutes'];
                $total_tardiness_freq += $aut['tardiness_freq'];
                $total_undertime_minutes += $aut['undertime_minutes'];
                $total_undertime_freq += $aut['undertime_freq'];

                # overtime store
                if ($matchedOvertime) {
                    $start = Carbon::parse($matchedOvertime->start_time);
                    $end = Carbon::parse($matchedOvertime->end_time);
                    $overtimeMinutes = $start->diffInMinutes($end);
                }  else {
                    $overtimeMinutes  = 0;
                }

                $formattedLogs[$dateString]['aut']['overtime']['minutes'] = $overtimeMinutes;

                # merge aut remarks to global remarks
                $remarks = array_merge($remarks, $aut['remarks']);

                $formattedLogs[$dateString]['remarks'] = array_merge(
                    $formattedLogs[$dateString]['remarks'] ?? [],
                    $remarks
                );

                $formattedLogs[$dateString]['isFuture'] = $isFuture;
                $formattedLogs[$dateString]['is_break_required'] = $is_break_required;
            } else {
                $formattedLogs[$dateString] = [
                    'bsd_no' => null,
                    'clock_in' => null,
                    'lunch_in' => null,
                    'lunch_out' => null,
                    'clock_out' => null,
                    'origin' => null,
                    'aut' => null,
                    'total_aut' => null,
                    'employee_no' => null,
                    'workOnHoliday' => null,
                    'isFuture' => $isFuture,
                    'remarks' => $remarks,
                    'is_break_required' => $is_break_required,
                ];
            }
        }

        $summary = [
            'leaves'                        => $leavesCount,
            'worked_days'                   => $workedDays,
            'absences'                      => $absences,
            'overtime'                      => $total_overtime_freq,
            'overtime_minues'               => $total_overtime_perminutes,
            'total_days_of_work'            => $workedDays + $absences,
            'less_aut'                      => 0,
            'tardiness_freq'                => $total_tardiness_perminutes,
            'tardiness'                     => $total_tardiness_freq,
            'undertime_freq'                => $total_undertime_minutes,
            'undertime'                     => $total_undertime_freq,
            'rest_days'                     => $restDays,
            'legal_hol'                     => $legalHolidays,
            'worked_on_legal_holidays'      => $workedOnLegalHolidays,
            'special_hol'                   => $specialHolidays,
            'worked_on_special_holidays'    => $workedOnSpecialHolidays,
        ];

        $data  =  [
            'formated_logs' => $formattedLogs,
            'summary' => $summary
        ];

        return $data;

    }
    private function getBsdNo($employee_no)
    {
        return DB::table('employee_information')
                ->where('employee_no', $employee_no)
                ->value('bsd_no');
    }
    private function getTotalLeaves($employeeNo, $dateInput)
    {
        $WHOLEDAY = 1;
        $HALFDAY = 0.5;
        $DURATION = 0;

        $query = DB::table('employee_leave_dates')
            ->leftJoin('employee_leave', 'employee_leave_dates.employee_leave_id', '=', 'employee_leave.id')
            ->select('employee_leave.duration', 'employee_leave_dates.date')
            ->where('employee_leave.employee_no', $employeeNo)
            ->where('employee_leave.status', 'approved');

        # Handle MM-YYYY
        if (is_string($dateInput) && preg_match('/^\d{2}-\d{4}$/', $dateInput)) {
            [$month, $year] = explode('-', $dateInput);
            $query->whereMonth('employee_leave_dates.date', (int) $month)
                ->whereYear('employee_leave_dates.date', (int) $year);

        # Handle date range
        } elseif (is_array($dateInput) && count($dateInput) === 2) {
            [$startDate, $endDate] = $dateInput;
            $query->whereBetween('employee_leave_dates.date', [$startDate, $endDate]);
        }

        $leaveDates = $query->get();

        foreach ($leaveDates as $date) {
            if ($date->duration === 'wholeday') {
                $DURATION += $WHOLEDAY;
            } else {
                $DURATION += $HALFDAY;
            }
        }

        return [
            'count' => $leaveDates->count(),
            'dates' => $leaveDates,
            'duration' => $DURATION,
        ];
    }
    private function checkAttendance($dateString,  $date_is_in_logs, $weeklySchedule, $dayName, $isFuture, $isLeave)
    {
        $isScheduled = $weeklySchedule->$dayName == 1;
        $ownRemarks = [];

        $absent = false;
        $workedDays = false;
        $restDays = false;
        $legalHolidays = false;
        $specialHolidays = false;
        $workedOnLegalHolidays = false;
        $workedOnSpecialHolidays = false;

        # Holiday check
        $holiday = $this->getHolidayByDate($dateString);

        $legalHolidays = false;
        $specialHolidays = false;

        $isHoliday = false;
        $isLegalHoliday = false;
        $isSpecialHoliday = false;

        if ($holiday) {
            $isHoliday = true;
            $type = strtolower($holiday->type);

           switch ($type) {
                case 'regular':
                case 'special-non-working':
                    $legalHolidays = true;
                    $isLegalHoliday = true;
                    break;
                case 'special-working':
                case 'company':
                    $specialHolidays = true;
                    $isSpecialHoliday = true;
                    break;
            }
        }

        $arrayWeeklySchedule = (array) $weeklySchedule;

        if (!$arrayWeeklySchedule[$dayName] && !$isHoliday) {
            $dayRemarkKey = $dayName . '_remarks';
            $restDays = true;
            $ownRemarks[] = $arrayWeeklySchedule[$dayRemarkKey];
        }

        if ($date_is_in_logs) {
            if ($isLegalHoliday) {
                $workedOnLegalHolidays = true;
                $ownRemarks[] = 'Legal Hol.';
            } elseif ($isSpecialHoliday) {
                $workedOnSpecialHolidays = true;
                $ownRemarks[] = 'Special Hol.';
            }
            $workedDays = true;
        } elseif ($isScheduled && !$isHoliday && !$isFuture && !$isLeave) {
            $absent = true;
            $ownRemarks[] = 'Absent';
        }

        $data = [
            'remarks' => $ownRemarks,
            'isAbsent' => $absent,
            'isWorkedDays' => $workedDays,
            'isRestDays' => $restDays,
            'isLegalHolidays' => $legalHolidays,
            'isSpecialHolidays' => $specialHolidays,
            'isWorkedOnLegalHolidays' => $workedOnLegalHolidays,
            'isWorkedOnSpecialHolidays' => $workedOnSpecialHolidays,
        ];

        return $data;
    }
    private function getWeeklySchedule($employee_no)
    {
        $weeklySchedule = DB::table('employee_schedules')
            ->leftJoin('employee_information', 'employee_schedules.id', '=', 'employee_information.schedule_id')
            ->select('employee_schedules.*')
            ->where('employee_information.employee_no', $employee_no)
            ->first();

        if (!$weeklySchedule) {
            throw new Exception("No Employee Schedule", 1);
        }

        return $weeklySchedule;
    }
    private function getHolidayByDate($date)
    {
        return DB::table('holidays')
            ->where('isDeleted', false)
            ->where('date', $date)
            ->first();
    }
    private function checkLeave($employee_no, $date)
    {
        $WHOLEDAY = 1;
        $HALFDAY = 0.5;
        $DURATION = 0;

        $query = DB::table('employee_leave_dates')
            ->leftJoin('employee_leave', 'employee_leave_dates.employee_leave_id', '=', 'employee_leave.id')
            ->select('employee_leave.duration', 'employee_leave_dates.date')
            ->where('employee_leave_dates.date', $date)
            ->where('employee_leave.employee_no', $employee_no)
            ->where('employee_leave.status', 'approved');

        $leaveDates = $query->get();

        foreach ($leaveDates as $date) {
            if ($date->duration === 'wholeday') {
                $DURATION += $WHOLEDAY;
            } else {
                $DURATION += $HALFDAY;
            }
        }

        return [
            'count' => $leaveDates->count(),
            'dates' => $leaveDates,
            'duration' => $DURATION,
        ];
    }
    private function undertimeAndTardiness($employee_no, $employeeSchedule, $log, $date)
    {
        $TARDINESS_MINUTES = 0;
        $TARDINESS_FREQ = 0;

        $UNDERTIME_MINUTES = 0;
        $UNDERTIME_FREQ = 0;

        $ownRemark = [];

        if ($employeeSchedule->is_breaktime_required) {
            $timeIn = $log['clock_in'];
            $breakOut = $log['lunch_out'];
            $breakIn = $log['lunch_in'];
            $timeOut = $log['clock_out'];
            if (!$timeIn || !$timeOut || !$breakOut || !$breakIn) {
                $ownRemark[] = 'Discrepancy';
            }
        } else {
            $timeIn = $log['clocn_in'];
            $timeOut = $log['clock_out'];
            $breakOut = $breakIn = null;
            if($timeIn == null || $timeOut == null) {
                $ownRemark[] = 'Discrepancy';
            }
        }

        $firstLog = Carbon::parse("{$date} {$timeIn}");
        $lastLog = Carbon::parse("{$date} {$timeOut}");

        # Get scheduled shift
        [$scheduledIn, $scheduledOut, $scheduledBreakIn, $scheduledBreakOut] = $this->getScheduledInOut($employeeSchedule, $date, $firstLog);

        if (!$scheduledIn || !$scheduledOut) {
            Log::warning("Missing schedule for {$employee_no} on {$date}");
        }

        # Tardiness
        if ($firstLog->greaterThan($scheduledIn)) {
            $minutesLate = $firstLog->diffInMinutes($scheduledIn);
            $TARDINESS_MINUTES += $minutesLate;
            $TARDINESS_FREQ++;
            $ownRemark[] = 'Late';
            Log::info("Tardiness for {$employee_no} on {$date}: {$minutesLate} minutes late");
        }

        # Undertime
        if ($lastLog->lessThan($scheduledOut)) {
            $minutesUndertime = $scheduledOut->diffInMinutes($lastLog);
            $UNDERTIME_MINUTES += $minutesUndertime;
            $UNDERTIME_FREQ++;
            $ownRemark[] = 'Undertime';
            Log::info("Undertime for {$employee_no} on {$date}: {$minutesUndertime} minutes undertime");
        }

        return [
            'tardiness_minutes' => $TARDINESS_MINUTES,
            'tardiness_freq' => $TARDINESS_FREQ,
            'undertime_minutes' => $UNDERTIME_MINUTES,
            'undertime_freq' => $UNDERTIME_FREQ,
            'remarks' => $ownRemark,
            'is_break_required' => $employeeSchedule->is_breaktime_required ?? false,
        ];
    }
    private function getShiftSchedule($employeeNo)
    {
        $employee_shift = DB::table('shift_schedule')
                        ->leftJoin('employee_information', 'shift_schedule.id', '=', 'employee_information.shift_id')
                        ->select('shift_schedule.*')
                        ->where('employee_information.employee_no', $employeeNo)
                        ->first();

        if (!$employee_shift) {
            throw new Exception("No shift schedule assigned", 1);
        }

        return $employee_shift;
    }
    private function getScheduledInOut($schedule, $date = null, $firstLog = null)
    {
        if ($schedule->work_setup === 'hybrid') {
            $in = Carbon::parse("{$date} {$schedule->latest_in}");

            if ($firstLog) {
                $firstLogTime = Carbon::parse($firstLog);
                if ($firstLogTime->lessThan($in)) {
                    $in = $firstLogTime;
                }
            }

            $out = (clone $in)->addHours($schedule->work_hours + 1);

            $breakOut = Carbon::parse("{$date} {$schedule->break_out}");
            $breakIn = Carbon::parse("{$date} {$schedule->break_in}");

            return [$in, $out, $breakOut, $breakIn];
        }

        if ($schedule->start_shift && $schedule->end_shift) {
            return [
                Carbon::parse("{$date} {$schedule->start_shift}"),
                Carbon::parse("{$date} {$schedule->end_shift}"),
                Carbon::parse("{$date} {$schedule->break_out}"),
                Carbon::parse("{$date} {$schedule->break_in}")
            ];
        }

        return [null, null, null, null];
    }
    private function getTotalOvertime($employeeNo, $dateInput) 
    {
        $query = DB::table('employee_atro')
            ->select('date', 'start_time', 'end_time')
            ->where('employee_no', $employeeNo)
            ->where('status', 'approved');

        if (is_string($dateInput) && preg_match('/^\d{2}-\d{4}$/', $dateInput)) {
            [$month, $year] = explode('-', $dateInput);
            $query->whereMonth('date', (int) $month)
                ->whereYear('date', (int) $year);

        } elseif (is_array($dateInput) && count($dateInput) === 2) {
            [$startDate, $endDate] = $dateInput;
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $overtimes = $query->get();
        $totalMinutes = 0;

        foreach ($overtimes as $overtime) {
            try {
                $start = Carbon::parse($overtime->start_time);
                $end = Carbon::parse($overtime->end_time);

                $minutes = $end->diffInMinutes($start);
                $totalMinutes += $minutes;
            } catch (\Exception $e) {
                continue;
            }
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return [
            'count' => $overtimes->count(),
            'dates' => $overtimes,
            'total_hours' => "{$hours} hr(s) {$minutes} min(s)",
            'raw_minutes' => $totalMinutes,
        ];
    }

    private function processLogs($employee, $logs, $monthYear, $isDTR = false)
    {
        $groupedLogs = [];

        foreach ($logs as $log) {
            $date = Carbon::parse($log->timestamp)->toDateString();

            $groupedLogs[$date][] = [
                'timestamp' => Carbon::parse($log->timestamp),
                'isWeb' => $log->isWeb,
                'captured_image' => $log->captured_image,
                'captured_location' => $log->captured_location,
                'accomplishment' => $log->accomplishment,
            ];
        }

        $processedLogs = [];

        foreach ($groupedLogs as $date => $entries) {
            $timestamps = collect($entries)->pluck('timestamp')->sort()->values();

            $firstEntry = $entries[0];

            $record = $this->initializeRecord($employee, $firstEntry, $date);

            $this->assignTimestamps($record, $timestamps);

            $processedLogs[$date] = $record;
        }

        return $processedLogs;
    }

    private function initializeRecord($employee, $log, $date)
    {
        return [
            'bsd_no' => is_array($employee) ? ($employee['bsd_no'] ?? $employee['employee_no']) : ($employee->bsd_no ?? $employee->employee_no),
            'clock_in' => null,
            'lunch_in' => null,
            'lunch_out' => null,
            'clock_out' => null,
            'origin' => null,
            'date' => $date,
            'aut' => [
                'tardiness' => ['minutes' => 0, 'reason' => null],
                'undertime' => ['minutes' => 0, 'reason' => null],
                'overtime' => ['minutes' => 0, 'reason' => null],
            ],
            'total_aut' => 0,
            'isWeb' => $log['isWeb'],
            'captured_image' => $log['captured_image'],
            'captured_location' => $log['captured_location'],
            'accomplishment' => $log['accomplishment'],
        ];
    }

    private function assignTimestamps(&$record, $timestamps) {
        if ($timestamps->count() >= 4) {
            $record['clock_in'] = $timestamps[0]->format('h:i A');
            $record['lunch_in'] = $timestamps[1]->format('h:i A');
            $record['lunch_out'] = $timestamps[$timestamps->count() - 2]->format('h:i A');
            $record['clock_out'] = $timestamps[$timestamps->count() - 1]->format('h:i A');
        } elseif ($timestamps->count() === 2) {
            # Special case: exactly two logs
            $record['clock_in'] = $timestamps[0]->format('h:i A');
            $record['clock_out'] = $timestamps[1]->format('h:i A');
        } elseif($timestamps->count() == 1) {
            $record['clock_in'] = $timestamps[0]->format('h:i A');
        } else {
            # Fallback: assign based on time ranges
            foreach ($timestamps as $ts) {
                $hour = (int) $ts->format('H');
                if (!isset($record['clock_in']) && $hour >= 5 && $hour <= 9) {
                    $record['clock_in'] = $ts->format('h:i A');
                } elseif (!isset($record['lunch_in']) && $hour >= 11 && $hour <= 12) {
                    $record['lunch_in'] = $ts->format('h:i A');
                } elseif (!isset($record['lunch_out']) && $hour >= 12 && $hour <= 13) {
                    $record['lunch_out'] = $ts->format('h:i A');
                } elseif (!isset($record['clock_out']) && $hour >= 15 && $hour <= 18) {
                    $record['clock_out'] = $ts->format('h:i A');
                }
            }
        }
    }
    
}   