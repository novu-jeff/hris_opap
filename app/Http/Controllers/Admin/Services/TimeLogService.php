<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\EmployeeTimelogs;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getDTR(string $biometrics_id, string $monthYear)
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

        $logs = $this->formatDayDTR($logs, $monthYear);
        $summary = $this->getSummary($logs);
        
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
                $this->calculateAUTO($record);
    
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
            'employee' => $employee,
        ];
    }

    private function assignTimestamps(&$record, $timestamps) {
        if ($timestamps->count() >= 4) {
            $record['clock_in'] = $timestamps[0]->format('h:i A');
            $record['lunch_in'] = $timestamps[1]->format('h:i A');
            $record['lunch_out'] = $timestamps[$timestamps->count() - 2]->format('h:i A');
            $record['clock_out'] = $timestamps[$timestamps->count() - 1]->format('h:i A');
        } else {
            foreach ($timestamps as $ts) {
                $hour = (int) $ts->format('H');
                if (!$record['clock_in'] && $hour >= 5 && $hour <= 9) {
                    $record['clock_in'] = $ts->format('h:i A');
                } elseif (!$record['lunch_in'] && $hour >= 11 && $hour <= 12) {
                    $record['lunch_in'] = $ts->format('h:i A');
                } elseif (!$record['lunch_out'] && $hour >= 12 && $hour <= 13) {
                    $record['lunch_out'] = $ts->format('h:i A');
                } elseif (!$record['clock_out'] && $hour >= 15 && $hour <= 18) {
                    $record['clock_out'] = $ts->format('h:i A');
                }
            }
        }
    }

    private function calculateAUTO(&$record)
    {

        $startTime = Carbon::createFromTime(7, 0);
        $latestAllowedIn = Carbon::createFromTime(9, 0);
        $breakStart = Carbon::createFromTime(12, 0);
        $minimumOvertime = 120;
    
        $record['remarks'] = [];
    
        $clock_in = $this->parseTime($record['clock_in']);
        $clock_out = $this->parseTime($record['clock_out']);
        $lunch_in = $this->parseTime($record['lunch_in']);
        $lunch_out = $this->parseTime($record['lunch_out']);
    
        if (!$clock_in && !$clock_out) {
            $record['remarks'][] = 'Absent';
            return;
        }
    
        $requiredFields = ['clock_in', 'lunch_in', 'lunch_out', 'clock_out'];
        foreach ($requiredFields as $field) {
            if (empty($record[$field])) {
                $record['remarks'][] = 'Discrepancy';
                break;
            }
        }
    
        $actualStart = $clock_in && $clock_in->lt($startTime) ? $startTime->copy() : $clock_in;
    
        // Tardiness
        if ($actualStart && $actualStart->gt($latestAllowedIn)) {
            $late = $actualStart->diffInMinutes($latestAllowedIn);
            $record['aut']['tardiness'] = [
                'minutes' => $late,
                'reason' => "Late by {$late} minute(s). Time-in at {$record['clock_in']}, beyond 09:00 AM.",
            ];
            $record['remarks'][] = 'Late';
        }
    
        if ($actualStart && $clock_out) {
            $totalWorked = $clock_out->diffInMinutes($actualStart);
    
            // Deduct lunch
            $lunchMinutes = 0;
            if ($lunch_in && $lunch_out && $lunch_out->gt($lunch_in)) {
                $lunchMinutes = $lunch_out->diffInMinutes($lunch_in);
                $totalWorked -= $lunchMinutes;
            } elseif ($actualStart->lt($breakStart) && $clock_out->gt(Carbon::createFromTime(13))) {
                $lunchMinutes = 60;
                $totalWorked -= 60;
            }
    
            // Deduct early lunch-in
            $earlyLunchDeduct = 0;
            if ($lunch_in && $lunch_in->lt($breakStart)) {
                $earlyLunchDeduct = $breakStart->diffInMinutes($lunch_in);
                $totalWorked -= $earlyLunchDeduct;
            }
    
            // CASE 1: Expected out used (normal range clock-in)
            if ($actualStart->betweenIncluded($startTime, $latestAllowedIn)) {
                $expectedOut = $actualStart->copy()->addHours(9); // 8 work hours + 1 lunch hour
                if ($clock_out->lt($expectedOut)) {
                    $ut = $expectedOut->diffInMinutes($clock_out);
                    $record['aut']['undertime'] = [
                        'minutes' => $ut,
                        'reason' => "Expected out at {$expectedOut->format('h:i A')} (8 hrs + 1 hr lunch), but clocked out at {$clock_out->format('h:i A')} ({$ut} min short).",
                    ];
                    $record['remarks'][] = 'Undertime';
                } elseif ($totalWorked >= 480 + $minimumOvertime) {
                    $ot = $totalWorked - 480;
                    $record['aut']['overtime'] = [
                        'minutes' => $ot,
                        'reason' => "Worked {$totalWorked} minutes, which is {$ot} minute(s) of overtime.",
                    ];
                    $record['remarks'][] = 'Overtime';
                }
            }
            // CASE 2: Irregular clock-in (before 7 AM or after 9 AM)
            else {
                if ($totalWorked < 480) {
                    $ut = 480 - $totalWorked;
                    $record['aut']['undertime'] = [
                        'minutes' => $ut,
                        'reason' => "Worked only {$totalWorked} minute(s), {$ut} minute(s) short of 480 minutes (irregular time-in).",
                    ];
                    $record['remarks'][] = 'Undertime';
                } elseif ($totalWorked >= 480 + $minimumOvertime) {
                    $ot = $totalWorked - 480;
                    $record['aut']['overtime'] = [
                        'minutes' => $ot,
                        'reason' => "Worked {$totalWorked} minutes, which is {$ot} minute(s) of overtime.",
                    ];
                    $record['remarks'][] = 'Overtime';
                }
            }
        }
    }
           

    private function parseTime(?string $time)
    {
        return $time ? Carbon::createFromFormat('h:i A', $time) : null;
    }

    private function formatDayDTR($logs, $monthYear)
    {
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
    
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
    
        $allLeaves = EmployeeLeave::whereIn('employee_no', $employeeNos)
            ->where(function ($query) use ($startDateFormatted, $endDateFormatted) {
                $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                    $q->whereDate('from', '<=', $endDateFormatted)
                      ->where(function ($q2) use ($startDateFormatted) {
                          $q2->whereDate('to', '>=', $startDateFormatted)
                              ->orWhereNull('to');
                      });
                });
            })
            ->get();
    
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateString = $date->toDateString();
            $isWeekend = $date->isSaturday() || $date->isSunday();
            $isFuture = $date->gt($today);
            $remarks = [];
    
            // 1. Holiday
            $holiday = Holiday::where('date', $date->format('m-d'))->first();
            if ($holiday) {
                $remarks[] = $holiday->type === 'regular' ? 'Legal Hol' : 'Special Hol';
            }
    
            // 2. Leave
            foreach ($allLeaves as $leave) {
                $origFrom = Carbon::parse($leave->from);
                $from = $origFrom->copy()->subDay();
                $to = $leave->to ? Carbon::parse($leave->to) : $from->copy()->addDay();
    
                if ($date->between($from, $to)) {
                    $remarks[] = 'Leave';
                    break;
                }
            }
    
            // 3. Weekend
            if ($isWeekend) {
                $remarks[] = 'Rest Day';
            }
    
            // 4. Absent
            if (!isset($logs[$dateString]) && !$isFuture && !in_array('Leave', $remarks) && !$isWeekend) {
                $remarks[] = 'Absent';
            }

    
            // Prioritize remarks
            $priorityRemarks = ['Leave', 'Legal Hol', 'Special Hol'];
            $intersect = array_intersect($remarks, $priorityRemarks);
            if (!empty($intersect)) {
                $remarks = array_values($intersect);
            }
    
            if (isset($logs[$dateString])) {
                $formattedLogs[$dateString] = $logs[$dateString];

                if ($isWeekend) {
                    $hasLog = !empty($logs[$dateString]['clock_in']) || !empty($logs[$dateString]['clock_out']);
                    if ($hasLog) {
                        $formattedLogs[$dateString]['workOnHoliday'] = true;
                    }
                }

                $formattedLogs[$dateString]['remarks'] = array_merge(
                    $formattedLogs[$dateString]['remarks'] ?? [],
                    $remarks
                );
            } else {
                $formattedLogs[$dateString] = [
                    'bsd_no' => null,
                    'clock_in' => null,
                    'lunch_in' => null,
                    'lunch_out' => null,
                    'clock_out' => null,
                    'origin' => null,
                    'aut' => null,
                    'employee_no' => null,
                    'workOnHoliday' => null,
                    'remarks' => $remarks,
                ];
            }
        }
    
        return $formattedLogs;
    }
    
    
    private function getSummary($logs)
    {
        $summary = [
            'leaves' => 0,
            'worked_days' => 0,
            'absences' => 0,
            'overtime' => 0,
            'total_days_of_work' => count($logs),
            'less_aut' => 0,
            'tardiness_freq' => 0,
            'tardiness' => 0,
            'undertime_freq' => 0,
            'undertime' => 0,
            'rest_days' => 0,
            'legal_hol' => 0,
            'special_hol' => 0,
        ];
    
        foreach ($logs as $date => $log) {
            $remarks = $log['remarks'] ?? [];
    
            if (is_array($remarks)) {
                foreach ($remarks as $remark) {
                    switch ($remark) {
                        case 'Absent':
                            $summary['absences']++;
                            break;
    
                        case 'Overtime':
                            $summary['overtime'] += $log['aut']['overtime']['minutes'] ?? 0;
                            break;
    
                        case 'Late':
                            if (isset($log['aut']['tardiness']['minutes'])) {
                                $lateMinutes = $log['aut']['tardiness']['minutes'];
                                $summary['tardiness_freq']++;
                                $summary['tardiness'] += $lateMinutes;
                            }
                            break;
    
                        case 'Undertime':
                            if (isset($log['aut']['undertime']['minutes'])) {
                                $undertimeMinutes = $log['aut']['undertime']['minutes'];
                                $summary['undertime_freq']++;
                                $summary['undertime'] += $undertimeMinutes;
                            }
                            break;
    
                        case 'Rest Day':
                            $summary['rest_days']++;
                            break;
    
                        case 'Legal Hol':
                            $summary['legal_hol']++;
                            break;
    
                        case 'Special Hol':
                            $summary['special_hol']++;
                            break;
    
                        case 'Leave':
                            $summary['leaves']++;
                            break;
                    }
                }
            }
    
            // Determine if the log has clock-in and clock-out
            $hasLog = !empty($log['clock_in']) && !empty($log['clock_out']);
    
            // If day is not Absent or Leave, OR it has time logs — count as worked
            $hasAbsentOrLeave = array_intersect($remarks, ['Absent', 'Leave']);
            if (empty($hasAbsentOrLeave)) {
                if (!empty(array_intersect($remarks, ['Legal Hol', 'Special Hol', 'Rest Day']))) {
                    if ($hasLog) {
                        $summary['worked_days']++;
                    }
                } else {
                    $summary['worked_days']++;
                }
            }
    
            // Add less_aut values
            if (isset($log['aut']['tardiness']['minutes'])) {
                $summary['less_aut'] += $log['aut']['tardiness']['minutes'];
            }
    
            if (isset($log['aut']['undertime']['minutes'])) {
                $summary['less_aut'] += $log['aut']['undertime']['minutes'];
            }
        }
    
        // Format numeric values to 2 decimal places
        $toMins = ['leaves', 'overtime', 'undertime', 'less_aut', 'tardiness'];
        foreach ($toMins as $key) {
            $summary[$key] = number_format((float)$summary[$key], 2, '.', '');
        }
    
        return $summary;
    }
    
    
}
