<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
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
            // Special case: exactly two logs
            $record['clock_in'] = $timestamps[0]->format('h:i A');
            $record['clock_out'] = $timestamps[1]->format('h:i A');
        } elseif($timestamps->count() == 1) {
            $record['clock_in'] = $timestamps[0]->format('h:i A');
        } else {
            // Fallback: assign based on time ranges
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

    private function calculateAUTO(&$record)
    {
        $startTime = Carbon::createFromTime(7, 0);
        $latestAllowedIn = Carbon::createFromTime(9, 0);
        $breakStart = Carbon::createFromTime(12, 0);
        $breakEnd = Carbon::createFromTime(13, 0);

        $record['remarks'] = [];
        $record['total_aut'] = 0;

        $clock_in = $this->parseTime($record['clock_in']);
        $clock_out = $this->parseTime($record['clock_out']);
        $lunch_in = $this->parseTime($record['lunch_in']);
        $lunch_out = $this->parseTime($record['lunch_out']);

        // Absent
        if (!$clock_in && !$clock_out && !$lunch_in && !$lunch_out) {
            $record['remarks'][] = 'Absent';
            return 480;
        }

        // Discrepancy
        if (!$clock_in || !$clock_out || !$lunch_in || !$lunch_out) {
            $record['remarks'][] = 'Discrepancy';
        }

        $actualStart = ($clock_in && $clock_in->lt($startTime)) ? $startTime : $clock_in;
        $totalAUT = 0;

        // Tardiness
        if ($actualStart && $actualStart->gt($latestAllowedIn)) {
            $late = $actualStart->diffInMinutes($latestAllowedIn);
            $record['aut']['tardiness'] = [
                'minutes' => $late,
                'reason' => "Late by {$late} minute(s). Time-in at {$record['clock_in']}, beyond 09:00 AM.",
            ];
            $record['remarks'][] = 'Late';
            $totalAUT += $late;
        }

        // Work Duration
        if ($actualStart && $clock_out) {
            $totalWorked = $clock_out->diffInMinutes($actualStart);

            // Deduct lunch break
            if ($lunch_in && $lunch_out && $lunch_out->gt($lunch_in)) {
                $totalWorked -= $lunch_out->diffInMinutes($lunch_in);
            } elseif ($actualStart->lt($breakStart) && $clock_out->gt($breakEnd)) {
                $totalWorked -= 60;
            }

            // Early lunch in penalty
            if ($lunch_in && $lunch_in->lt($breakStart)) {
                $totalWorked -= $breakStart->diffInMinutes($lunch_in);
            }

            // Undertime
            $expectedWorkMinutes = 480; // 8 hours
            if ($actualStart->betweenIncluded($startTime, $latestAllowedIn)) {
                $expectedOut = $actualStart->copy()->addHours(9); // 8 + 1 (lunch)
                if ($clock_out->lt($expectedOut)) {
                    $ut = $expectedOut->diffInMinutes($clock_out);
                    $record['aut']['undertime'] = [
                        'minutes' => $ut,
                        'reason' => "Expected out at {$expectedOut->format('h:i A')} (9 hrs incl. lunch), but clocked out at {$clock_out->format('h:i A')} ({$ut} min short).",
                    ];
                    $record['remarks'][] = 'Undertime';
                    $totalAUT += $ut;
                }
            } elseif ($totalWorked < $expectedWorkMinutes) {
                $ut = $expectedWorkMinutes - $totalWorked;
                $record['aut']['undertime'] = [
                    'minutes' => $ut,
                    'reason' => "Worked only {$totalWorked} minute(s), {$ut} minute(s) short of 480 minutes.",
                ];
                $record['remarks'][] = 'Undertime';
                $totalAUT += $ut;
            }
        }
        
        if (isset($record['date'])) {
            $dateStr = $record['date'];
            $formats = ['m-Y', 'Y-m-d'];
            $month = null;
            $year = null;

            foreach ($formats as $format) {
                try {
                    $dt = Carbon::createFromFormat($format, $dateStr);
                    if ($dt && $dt->format($format) === $dateStr) {
                        $month = strtolower($dt->format('F'));
                        $year = $dt->format('Y');
                        break;
                    }
                } catch (\Exception $e) {
                    // ignore, try next
                }
            }

            if ($month && $year) {
                $leaveCard = (new LeaveCardService())->leaveCard($record['employee_no']);
                $leaveItems = $leaveCard[$year]['items'] ?? null;

                if ($leaveItems) {
                    $hasAutWoPay = $leaveItems->filter(
                        fn($item) => strtolower($item->period) === $month && !empty($item->vl_aut_wo_pay)
                    )->isNotEmpty();

                    if ($hasAutWoPay) {
                        $record['total_aut'] = $totalAUT;
                    }
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
    
    public function getDTRByRange(string $biometrics_id, string $monthYear, $range) {

        $dtr = $this->getDTR($biometrics_id, $monthYear);

        $logs = $dtr['logs'];

        [$start, $end] = explode(' to ', $range);
        $startDate = Carbon::parse($start)->format('Y-m-d');
        $endDate = Carbon::parse($end)->format('Y-m-d');

        $filteredLogs = collect($logs)->filter(function ($value, $key) use ($startDate, $endDate) {
            return $key >= $startDate && $key <= $endDate;
        });

        $totalOvertime = 0;
        $totalAUT = 0;
        $totalWorkedDays = 0;
        
        foreach ($filteredLogs as $date => $log) {
            // Count worked days
            if (!empty($log['clock_in']) && !empty($log['clock_out'])) {
                $totalWorkedDays++;
            }
        
            // Compute AUT and Overtime if present
            if (isset($log['aut']) && is_array($log['aut'])) {
                $tardiness = $log['aut']['tardiness']['minutes'] ?? 0;
                $undertime = $log['aut']['undertime']['minutes'] ?? 0;
                $overtime = $log['aut']['overtime']['minutes'] ?? 0;
        
                $totalOvertime += $overtime;
                $totalAUT += ($tardiness + $undertime);
            }
        }
        

        $summary = [
            'worked_days' => $totalWorkedDays ?? 0,
            'overtime' => $totalOvertime ?? 0,
            'aut' => $totalAUT ?? 0
        ];

        return $summary;

    }
    
}
