<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Services\SummaryServices;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveDates;
use App\Models\EmployeeTimelogs;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimeLogService extends Controller
{
    public function getLogs(?string $timestamp = null)
    {
        $logs = EmployeeTimelogs::with('employee.personal')
            ->when($timestamp, fn($q) => $q->where('timestamp', 'like', "{$timestamp}%"))
            ->orderBy('timestamp')
            ->get();

        return $this->processLogs($logs, $timestamp);
    }

    public function getDTR(string $employeeNo, string $biometrics_id, string $monthYear)
    {

        if (empty($biometrics_id)) {
            abort(400, 'Invalid Biometrics Id format. Use MM-YYYY.');
        }

        try {
            $monthCarbon = Carbon::createFromFormat('m-Y', $monthYear);
            $startDate = $monthCarbon->startOfMonth()->toDateString();
            $endDate = $monthCarbon->endOfMonth()->toDateString();
        } catch (\Exception $e) {
            abort(400, 'Invalid month-year format. Use MM-YYYY.');
        }

        $logs = EmployeeTimelogs::where('employee_id', $biometrics_id)
            ->whereBetween('timestamp', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->orderBy('timestamp')
            ->get();


        $logs = $this->processLogs($logs, $monthYear, true);

        $logs = $this->formatDayDTR($employeeNo, $logs, $monthYear);
        $summary = $this->getSummary($employeeNo, $monthYear);

        return [
            'logs' => $logs,
            'summary' => $summary
        ];
    }

    private function processLogs($logs, $monthYear, $isDTR = false) {

        $grouped = [];

        foreach ($logs as $log) {
            $date = Carbon::parse($log->timestamp)->toDateString();
            $employeeId = $log->employee_id;

            $grouped[$date][$employeeId]['timestamps'][] = Carbon::parse($log->timestamp);
            $grouped[$date][$employeeId]['employee'] = $log->employee;
        }

        $final = [];

        foreach ($grouped as $date => $employees) {
            foreach ($employees as $employeeId => $data) {
                $timestamps = collect($data['timestamps'])
                    ->map(fn($ts) => Carbon::parse($ts))
                    ->sort()
                    ->values();

                $employee = $data['employee'];
                $record = $this->initializeRecord($employeeId, $employee, $monthYear);

                $this->assignTimestamps($record, $timestamps);

                if ($isDTR) {
                    $final[$date] = $record;
                } else {
                    $final[$date][$employeeId] = $record;
                }
            }
        }

        return $final;
    }

    private function initializeRecord($employeeId, $employee, $monthYear) {
        return [
            'employee_no' => $employee->employee_no ?? '',
            'bsd_no' => $employeeId,
            'clock_in' => null,
            'lunch_in' => null,
            'lunch_out' => null,
            'clock_out' => null,
            'origin' => 'biometrics',
            'date' => $monthYear,
            'aut' => [
                'tardiness' => ['minutes' => 0, 'reason' => null],
                'undertime' => ['minutes' => 0, 'reason' => null],
                'overtime' => ['minutes' => 0, 'reason' => null],
            ],
            'total_aut' => 0,
            'employee' => $employee,
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

    private function parseTime(?string $time)
    {
        return $time ? Carbon::createFromFormat('h:i A', $time) : null;
    }

    private function getWeeklySchedule(string $employeeId)
    {
        $weeklySchedule = DB::table('employee_schedules')
            ->leftJoin('employee_information', 'employee_schedules.id', '=', 'employee_information.schedule_id')
            ->select('employee_schedules.*')
            ->where('employee_information.employee_no', $employeeId)
            ->first();

        return (array) $weeklySchedule;
    }

    private function formatDayDTR($employee_no, $logs, $monthYear)
    {
        $getAUT = app(SummaryServices::class);

        try {
            $monthCarbon = Carbon::createFromFormat('m-Y', $monthYear);
            $startDate = $monthCarbon->copy()->startOfMonth();
            $endDate = $monthCarbon->copy()->endOfMonth();
        } catch (\Exception $e) {
            abort(400, 'Invalid month-year format. Use MM-YYYY.');
        }

        $today = Carbon::today();
        $formattedLogs = [];

        $employeeNos = collect($logs)->pluck('employee_no')->unique()->filter()->values();

        $allLeaves = EmployeeLeaveDates::whereIn('employee_no', $employeeNos)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function ($leave) {
                return $leave->employee_no . '|' . $leave->date;
            });

            
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateString = $date->toDateString();
            $isFuture = $date->gt($today);
            $remarks = [];
            $dayName = strtolower(Carbon::parse($dateString)->format('l'));
            $isRestDay = false;

            # 1. Holiday
            $holiday = Holiday::where('date', $date->format('m-d'))->first();
            if ($holiday) {
               $remarks[] = in_array($holiday->type, ['regular', 'special-working-holiday'])
                    ? 'Legal Hol'
                    : 'Special Hol';
            }

            # 2. Leave
            foreach ($employeeNos as $employeeNo) {
                $leaveKey = $employeeNo . '|' . $dateString;
                if (isset($allLeaves[$leaveKey])) {
                    $remarks[] = 'Leave';
                    break;
                }
            }

            # 3. Rest day
            $weeklySchedule = $this->getWeeklySchedule($employee_no);
            
            if(!$weeklySchedule[$dayName]) {
                $dayRemark = strtolower($today->format('l')) . '_remarks';
                $isRestDay = true;
                $remarks[] = $weeklySchedule[$dayRemark];
            }
                        
            # # 4. Absent
            if (!isset($logs[$dateString]) && !$isRestDay && !$isFuture && !in_array('Leave', $remarks)) {
                $remarks[] = 'Absent';
            }

            # Prioritize remarks
            $priorityRemarks = ['Leave', 'Legal Hol', 'Special Hol'];
            $intersect = array_intersect($remarks, $priorityRemarks);
            if (!empty($intersect)) {
                $remarks = array_values($intersect);
            }
            
            if (isset($logs[$dateString])) {
                $employeeNo = $logs[$dateString]['employee_no'];
                $formattedLogs[$dateString] = $logs[$dateString];
                
                $lateAndUndertimeRemarks = $getAUT->getTardinessAndUndertime(
                    $employeeNo,
                    $logs[$dateString]['clock_in'],
                    $logs[$dateString]['clock_out']
                );

                if($logs[$dateString]['clock_in'] == null || $logs[$dateString]['clock_out'] == null) {
                    $remarks[] = 'Descrepancy';
                }
                
                $remarks = array_merge($remarks, $lateAndUndertimeRemarks);

                if ($isRestDay) {
                    $hasLog = !empty($logs[$dateString]['clock_in']) || !empty($logs[$dateString]['clock_out']);
                    if ($hasLog) {
                        $formattedLogs[$dateString]['workOnHoliday'] = true;
                    }
                }

                $formattedLogs[$dateString]['remarks'] = array_merge(
                    $formattedLogs[$dateString]['remarks'] ?? [],
                    $remarks
                );

                $formattedLogs[$dateString]['isFuture'] = $isFuture;
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
                ];
            }
        }

        return $formattedLogs;
    }

    private function getSummary($employee_no, $dateInput)
    {
        $summaryService = app(SummaryServices::class);

        $summary = $summaryService->getSummary($employee_no, $dateInput);

        return $summary;
    }

    public function getDTRByRange(string $biometrics_id, string $range): array
    {
        if (empty($biometrics_id)) {
            abort(400, 'Biometrics ID is required.');
        }

        try {
            [$start, $end] = explode(' to ', $range);
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } catch (\Exception $e) {
            abort(400, 'Invalid date range format. Use "YYYY-MM-DD to YYYY-MM-DD".');
        }

        # Get logs only within range
        $rawLogs = EmployeeTimelogs::where('employee_id', $biometrics_id)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();

        # Process and format logs
        $processedLogs = $this->processLogs($rawLogs, $startDate->format('m-Y'), true);
        $formattedLogs = $this->formatDayDTR($processedLogs, $startDate->format('m-Y'));

        # Filter logs again for safety
        $filteredLogs = collect($formattedLogs)->filter(function ($value, $key) use ($startDate, $endDate) {
            return $key >= $startDate->toDateString() && $key <= $endDate->toDateString();
        });

        # Initialize counters
        $totalWorkedDays = 0;
        $totalOvertime = 0;
        $totalAUT = 0;

        foreach ($filteredLogs as $log) {
            if (!empty($log['clock_in'])) {
                $totalWorkedDays++;
            }

            if (isset($log['aut']) && is_array($log['aut'])) {
                $tardiness = $log['aut']['tardiness']['minutes'] ?? 0;
                $undertime = $log['aut']['undertime']['minutes'] ?? 0;
                $overtime = $log['aut']['overtime']['minutes'] ?? 0;

                $totalOvertime += $overtime;
                $totalAUT += $tardiness + $undertime;
            }
        }

        # Compute total days in the range (inclusive)
        $totalDays = $startDate->diffInDays($endDate) + 1;

        return [
            'logs' => $filteredLogs,
            'summary' => [
                'total_days' => $totalDays,
                'worked_days' => $totalWorkedDays,
                'overtime' => $totalOvertime,
                'aut' => $totalAUT,
            ],
        ];
    }
}
