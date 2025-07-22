<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SummaryServices {

    public function getTotalLeaves($employeeNo, $dateInput)
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
    
    public function getTotalOvertime($employeeNo, $dateInput) 
    {
        $query = DB::table('employee_atro')
            ->select('date', 'start_time', 'end_time')
            ->where('employee_no', $employeeNo)
            ->where('status', 'approved');

        # Handle MM-YYYY format
        if (is_string($dateInput) && preg_match('/^\d{2}-\d{4}$/', $dateInput)) {
            [$month, $year] = explode('-', $dateInput);
            $query->whereMonth('date', (int) $month)
                ->whereYear('date', (int) $year);

        # Handle date range format
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
                continue; # skip any invalid time format
            }
        }

        # Convert to hours/minutes
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return [
            'count' => $overtimes->count(),
            'dates' => $overtimes,
            'total_hours' => "{$hours} hr(s) {$minutes} min(s)",
            'raw_minutes' => $totalMinutes,
        ];
    }

    public function getTardinessAndUndertime($employeeNo, $timeIn, $timeOut)
    {
        $remarks = [];

        $timeIn = Carbon::parse($timeIn);
        $timeOut = Carbon::parse($timeOut);

        $employeeSchedule = $this->getShiftSchedule($employeeNo);

        [$scheduledIn, $scheduledOut] = $this->getScheduledInOut($employeeSchedule, null, $timeIn);

        # TARDINESS
        if ($timeIn->greaterThan($scheduledIn)) {
            $remarks[] = 'Late';
        }

        # UNDERTIME
        if ($timeOut->lessThan($scheduledOut)) {
            $remarks[] = 'Undertime';
        }

        return $remarks;
    }

    public function getAUT($employeeNo, $dateInput, $biometricId = null)
    {
        $TARDINESS_MINUTES = 0;
        $TARDINESS_FREQ = 0;

        $UNDERTIME_MINUTES = 0;
        $UNDERTIME_FREQ = 0;

        $employeeSchedule = $this->getShiftSchedule($employeeNo);

        $timelogs = DB::table('timelogs');

        if (!empty($employeeNo)) {
            $timelogs->where('employee_id', $employeeNo);
        } elseif (!empty($biometricId)) {
            $timelogs->where('biometric_id', $biometricId);
        }

        if (is_string($dateInput) && preg_match('/^\d{2}-\d{4}$/', $dateInput)) {
            [$month, $year] = explode('-', $dateInput);
            $timelogs->whereMonth('timestamp', (int)$month)
                    ->whereYear('timestamp', (int)$year);
        } elseif (is_array($dateInput) && count($dateInput) === 2) {
            [$startDate, $endDate] = $dateInput;
            $timelogs->whereBetween('timestamp', [$startDate, $endDate]);
        }

        $logs = $timelogs->orderBy('timestamp')->get()
            ->groupBy(function ($log) {
                return Carbon::parse($log->timestamp)->toDateString();
            });

        foreach ($logs as $date => $entries) {
            $timeIn = $entries->where('status', 0)->sortBy('timestamp')->first();
            $timeOut = $entries->where('status', 1)->sortByDesc('timestamp')->first();

            if (!$timeIn || !$timeOut) {
                continue;
            }

            $firstLog = Carbon::parse($timeIn->timestamp);
            $lastLog = Carbon::parse($timeOut->timestamp);

            # Determine scheduled in and out based on work setup
            [$scheduledIn, $scheduledOut] = $this->getScheduledInOut($employeeSchedule, $date, $firstLog);


            if (!$scheduledIn || !$scheduledOut) {
                continue; # skip if shift cannot be determined
            }

            # TARDINESS
            if ($firstLog->greaterThan($scheduledIn)) {
                $minutesLate = $firstLog->diffInMinutes($scheduledIn);
                $TARDINESS_MINUTES += $minutesLate;
                $TARDINESS_FREQ++;
            }

            # UNDERTIME
            if ($lastLog->lessThan($scheduledOut)) {
                $minutesUndertime = $scheduledOut->diffInMinutes($lastLog);
                $UNDERTIME_MINUTES += $minutesUndertime;
                $UNDERTIME_FREQ++;
            }

        }

        $attendanceWithoutFutureDates = $this->getAttendanceWithOptionalHolidayWork($employeeNo, $dateInput);

        return [
            'tardiness_minutes' => $TARDINESS_MINUTES,
            'tardiness_freq' => $TARDINESS_FREQ,
            'undertime_minutes' => $UNDERTIME_MINUTES,
            'undertime_freq' => $UNDERTIME_FREQ,
            'absences' => $attendanceWithoutFutureDates['absences'],
            'worked_days' => $attendanceWithoutFutureDates['worked_days'],
            'rest_days' => $attendanceWithoutFutureDates['rest_days'],
            'legal_holidays' => $attendanceWithoutFutureDates['legal_holidays'],
            'worked_on_legal_holidays' => $attendanceWithoutFutureDates['worked_on_legal_holidays'],
            'special_holidays' => $attendanceWithoutFutureDates['special_holidays'],
            'worked_on_special_holidays' => $attendanceWithoutFutureDates['worked_on_special_holidays'],
            'total_days_of_work' => $attendanceWithoutFutureDates['total_days_of_work']
        ];
    }

    public function getSummary($employeeNo, $dateInput, $biometricId = null)
    {
        $leaveDates = $this->getTotalLeaves($employeeNo, $dateInput, $biometricId);
        $atro = $this->getTotalOvertime($employeeNo, $dateInput);
        $AUT = $this->getAUT($employeeNo, $dateInput);
        
        $data = [
            'leaves'                        => $leaveDates['count'],
            'worked_days'                   => $AUT['worked_days'],
            'absences'                      => $AUT['absences'],
            'overtime'                      => $atro['count'],
            'total_days_of_work'            => $AUT['total_days_of_work'],
            'less_aut'                      => 0,
            'tardiness_freq'                => $AUT['tardiness_freq'],
            'tardiness'                     => $AUT['tardiness_minutes'],
            'undertime_freq'                => $AUT['undertime_freq'],
            'undertime'                     => $AUT['undertime_minutes'],
            'rest_days'                     => $AUT['rest_days'],
            'legal_hol'                     => $AUT['legal_holidays'],
            'worked_on_legal_holidays'      => $AUT['worked_on_legal_holidays'],
            'special_hol'                   => $AUT['special_holidays'],
            'worked_on_special_holidays'    => $AUT['worked_on_special_holidays'],
        ];

        return $data;
    }

    protected function getScheduledInOut($schedule, $date = null, $firstLog)
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

            return [$in, $out];
        }

        if ($schedule->start_shift && $schedule->end_shift) {
            return [
                Carbon::parse("{$date} {$schedule->start_shift}"),
                Carbon::parse("{$date} {$schedule->end_shift}"),
            ];
        }

        return [null, null];
    }

    protected function getShiftSchedule($employeeNo)
    {
        $employee_shift = DB::table('shift_schedule')
                        ->leftJoin('employee_information', 'shift_schedule.id', '=', 'employee_information.shift_id')
                        ->select('shift_schedule.*')
                        ->where('employee_information.employee_no', $employeeNo)
                        ->first();

        return $employee_shift;
    }

    protected function getAttendanceWithOptionalHolidayWork(string $employeeId, $dateInput)
    {
        $weeklySchedule = DB::table('employee_schedules')
            ->leftJoin('employee_information', 'employee_schedules.id', '=', 'employee_information.schedule_id')
            ->select('employee_schedules.*')
            ->where('employee_information.employee_no', $employeeId)
            ->first();

        // Determine date range
        if (str_contains($dateInput, ' to ')) {
            [$start, $end] = explode(' to ', $dateInput);
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } else {
            [$month, $year] = explode('-', $dateInput);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
        }

        $today = Carbon::now()->endOfDay();
        $dayCounter = $startDate->copy();

        // Counters
        $absences = 0;
        $workedDays = 0;
        $restDays = 0;
        $legalHolidays = 0;
        $specialHolidays = 0;
        $workedOnLegalHolidays = 0;
        $workedOnSpecialHolidays = 0;

        while ($dayCounter->lte($endDate)) {
            if ($dayCounter->gt($today)) {
                break;
            }

            $dayName = strtolower($dayCounter->format('l'));
            $isScheduled = $weeklySchedule->$dayName == 1;

            // Holiday check
            $holiday = DB::table('holidays')
                ->where('isDeleted', false)
                ->where('date', $dayCounter->format('m-d'))
                ->first();

            $isHoliday = false;
            $isLegalHoliday = false;
            $isSpecialHoliday = false;

            if ($holiday) {
                $isHoliday = true;
                $type = strtolower($holiday->type);

                if ($type === 'regular' || $type === 'special-non-working') {
                    $legalHolidays++;
                    $isLegalHoliday = true;
                } elseif ($type === 'special-working' || $type === 'company') {
                    $specialHolidays++;
                    $isSpecialHoliday = true;
                }
            }

            $hasTimeLog = DB::table('timelogs')
                ->where('employee_id', $employeeId)
                ->whereDate('timestamp', $dayCounter->format('Y-m-d'))
                ->exists();

            if ($hasTimeLog) {
                if ($isLegalHoliday) {
                    $workedOnLegalHolidays++;
                } elseif ($isSpecialHoliday) {
                    $workedOnSpecialHolidays++;
                }
                $workedDays++;
            } elseif ($isScheduled && !$isHoliday) {
                $absences++;
            } elseif (!$isScheduled && !$isHoliday) {
                $restDays++;
            }

            $dayCounter->addDay();
        }

        return [
            'absences' => $absences,
            'worked_days' => $workedDays,
            'rest_days' => $restDays,
            'legal_holidays' => $legalHolidays,
            'special_holidays' => $specialHolidays,
            'worked_on_legal_holidays' => $workedOnLegalHolidays,
            'worked_on_special_holidays' => $workedOnSpecialHolidays,
            'total_days_of_work' => $workedDays + $absences + $workedOnLegalHolidays + $workedOnSpecialHolidays,
        ];
    }
}