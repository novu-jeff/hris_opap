<?php

namespace App\Http\Controllers\Admin\Services;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\EmployeeLeaveDates;
use App\Models\Holiday;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\TimeEquivalent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use InvalidArgumentException;

class LeaveCardService extends Controller
{

    public function init(string $employee_no, string $action, $data = null) {

        if($employee_no) {

            if($action == 'firstime') {
                $this->triggerSLVLFirst($data);
            }

            if($action == 'leave_approval') {

                $this->triggerSLVL($data);
            }
        }
    }

    public function triggerSLVL($data)
    {
        if (!$data) return;

        $employee_no = $data->employee_no;
        $currentYear = now()->year;

        $leaveType = LeaveType::find($data->leave_id);
        $leaveCode = strtolower($leaveType->code ?? '');

        // Get all leave dates from EmployeeLeaveDates
        $leaveDates = EmployeeLeaveDates::where('employee_leave_id', $data->id)->pluck('date')->map(fn ($d) => Carbon::parse($d));

        if ($leaveDates->isEmpty()) return;

        $latestCard = EmployeeLeaveCard::where('employee_no', $employee_no)
            ->where('year', $currentYear)
            ->orderBy('year', 'asc')
            ->get()
            ->last();

        $leaveCardBalance = match (true) {
            in_array($leaveCode, ['vl', 'sl']) => (float) ($latestCard?->{$leaveCode . '_bal'} ?? 0),
            default => (float) ($latestCard?->vl_bal ?? 0),
        };

        // Build leave months from actual leave dates
        $leaveMonths = $this->processLeaveDates($leaveDates, $leaveCode, $leaveCardBalance);

        // Latest cards to update
        $latestLeaveCard = EmployeeLeaveCard::where('employee_no', $employee_no)
            ->where('year', $currentYear)
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();

        $mapped = array_map(function ($leaveCard) use ($leaveMonths, $leaveCode) {
        $match = collect($leaveMonths)->firstWhere('month', $leaveCard['period']);

        if ($match) {
            if (in_array($leaveCode, ['vl', 'sl'])) {
                $leaveCard[$leaveCode . '_aut_w_pay'] =
                    floatval($leaveCard[$leaveCode . '_aut_w_pay'] ?? 0) +
                    floatval($match[$leaveCode . '_aut_w_pay'] ?? 0);

                $leaveCard[$leaveCode . '_aut_wo_pay'] =
                    floatval($leaveCard[$leaveCode . '_aut_wo_pay'] ?? 0) +
                    floatval($match[$leaveCode . '_aut_wo_pay'] ?? 0);
            } else {
                $leaveCard['vl_aut_w_pay'] =
                    floatval($leaveCard['vl_aut_w_pay'] ?? 0) +
                    floatval($match['vl_aut_w_pay'] ?? 0);
            }

            $fieldKeys = $leaveCode === 'mfl'
                ? ['particulars', 'remarks']
                : [in_array($leaveCode, ['vl', 'sl']) ? 'particulars' : 'remarks'];

            foreach ($fieldKeys as $key) {
                $existing = !empty($leaveCard[$key]) ? explode(', ', trim($leaveCard[$key])) : [];
                $new = !empty($match[$key]) ? explode(', ', trim($match[$key])) : [];
                $merged = array_filter(array_unique(array_merge($existing, $new)));
                $leaveCard[$key] = implode(', ', $merged);
            }
        } else {
            if (in_array($leaveCode, ['vl', 'sl'])) {
                $leaveCard[$leaveCode . '_aut_w_pay'] = '';
                $leaveCard[$leaveCode . '_aut_wo_pay'] = '';
                $leaveCard['particulars'] = '';
            } else {
                $leaveCard['remarks'] = '';
            }
        }

        return $leaveCard;
    }, $latestLeaveCard);


        $combined = $this->combine($mapped, $latestLeaveCard);
        $newData = $this->compute($combined);

        foreach ($newData as $info) {
            EmployeeLeaveCard::updateOrCreate(
                ['employee_no' => $info['employee_no'], 'period' => $info['period'], 'year' => $info['year']],
                [
                    'particulars' => $info['particulars'],
                    'vl_earned' => $info['vl_earned'],
                    'vl_aut_w_pay' => $info['vl_aut_w_pay'],
                    'vl_bal' => $info['vl_bal'],
                    'vl_aut_wo_pay' => $info['vl_aut_wo_pay'],
                    'sl_earned' => $info['sl_earned'],
                    'sl_aut_w_pay' => $info['sl_aut_w_pay'],
                    'sl_bal' => $info['sl_bal'],
                    'sl_aut_wo_pay' => $info['sl_aut_wo_pay'],
                    'remarks' => $info['remarks'],
                ]
            );
        }

        // Deduct credits only for other types
        if (!in_array($leaveCode, ['vl', 'sl', 'mfl'])) {
            $credit = LeaveCredits::where('employee_no', $employee_no)
                ->where('leave_type_id', $data->leave_id)
                ->first();

            if ($credit) {
                $credit->credits -= $data->daysCovered;
                $credit->as_of = now()->format('Y-m');
                $credit->save();
            }
        }
    }


    private function processLeaveDates(Collection $leaveDates, string $leaveCode, float $leaveBalance): array
    {

        $holidays = Holiday::pluck('date')->toArray();

        $leaveMonths = [];

        foreach ($leaveDates as $date) {
            $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);

            $month = strtoupper($carbonDate->format('F'));
            $abbrMonth = $carbonDate->format('M');
            $day = (int) $carbonDate->format('d');
            $dayOfWeek = $carbonDate->format('D');
            $formattedDate = $carbonDate->format('m-d');

            if (!isset($leaveMonths[$month])) {
                $leaveMonths[$month] = [
                    'month' => $month,
                    'days' => [],
                    'remarks' => '',
                ];

                if (in_array($leaveCode, ['vl', 'sl'])) {
                    $leaveMonths[$month][$leaveCode . '_aut_w_pay'] = 0;
                    $leaveMonths[$month][$leaveCode . '_aut_wo_pay'] = 0;
                    $leaveMonths[$month]['particulars'] = strtoupper($leaveCode) . ': ' . $abbrMonth;
                } elseif ($leaveCode === 'mfl') {
                    $leaveMonths[$month]['vl_aut_w_pay'] = 0;
                    $leaveMonths[$month]['vl_aut_wo_pay'] = 0;
                    $leaveMonths[$month]['particulars'] = strtoupper($leaveCode) . ': ' . $abbrMonth;
                    $leaveMonths[$month]['remarks'] = strtoupper($leaveCode) . ': ' . $abbrMonth;
                } else {
                    $leaveMonths[$month]['remarks'] = strtoupper($leaveCode) . ': ' . $abbrMonth;
                }
            }

            // Exclude weekends and holidays
            if ($dayOfWeek === 'Sat' || $dayOfWeek === 'Sun' || in_array($formattedDate, $holidays)) {
                $leaveMonths[$month]['remarks'] .= ($leaveMonths[$month]['remarks'] ? ', ' : 'Except, ') . "$abbrMonth $day";
            } else {
                $leaveMonths[$month]['days'][] = $day;
            }
        }

        foreach ($leaveMonths as &$entry) {
            $leaveDays = count($entry['days']);

            if (in_array($leaveCode, ['vl', 'sl'])) {
                $entry[$leaveCode . '_aut_w_pay'] = min($leaveBalance, $leaveDays);
                $entry[$leaveCode . '_aut_wo_pay'] = max(0, $leaveDays - $leaveBalance);
            } else {
                $entry['vl_aut_w_pay'] = min($leaveBalance, $leaveDays);
                $entry['vl_aut_wo_pay'] = max(0, $leaveDays - $leaveBalance);
            }

            // Group consecutive days
            sort($entry['days']);
            $ranges = [];
            $rangeStart = $prev = null;

            foreach ($entry['days'] as $day) {
                if ($rangeStart === null) {
                    $rangeStart = $day;
                } elseif ($day !== $prev + 1) {
                    $ranges[] = ($rangeStart === $prev) ? "$rangeStart" : "$rangeStart-$prev";
                    $rangeStart = $day;
                }
                $prev = $day;
            }

            if ($rangeStart !== null) {
                $ranges[] = ($rangeStart === $prev) ? "$rangeStart" : "$rangeStart-$prev";
            }

            $dayRanges = implode(', ', $ranges);

            if (in_array($leaveCode, ['vl', 'sl'])) {
                $entry['particulars'] .= ' ' . $dayRanges;
            } elseif ($leaveCode === 'mfl') {
                $entry['particulars'] .= ' ' . $dayRanges;
                $entry['remarks'] .= ' ' . $dayRanges;
            } else {
                $entry['remarks'] .= ' ' . $dayRanges;
            }
        }

        return array_values($leaveMonths);
    }

    private function combine($a, $b) {
        $mergedData = [];

        foreach ($a as $index => $itemA) {
            $itemB = $b[$index] ?? [];
            $mergedItem = [];

            foreach ($itemA as $key => $valueA) {
                $valueB = $itemB[$key] ?? null;

                // Normalize values to remove unwanted characters
                $valueA = trim(preg_replace('/\s+/', ' ', $valueA ?? ''));
                $valueB = trim(preg_replace('/\s+/', ' ', $valueB ?? ''));

                if ($key === 'particulars' && $valueB !== null) {
                    // Convert to an array, filter out empty values and ''
                    $values = array_filter(
                        array_merge(explode(', ', $valueA), explode(', ', $valueB)),
                        fn($v) => $v !== '' && trim($v) !== ''
                    );
                    // Remove duplicates and reformat
                    $mergedValue = implode(', ', array_unique($values));

                } elseif (in_array($key, ['vl_aut_w_pay', 'sl_aut_w_pay', 'vl_aut_wo_pay', 'sl_aut_wo_pay'])) {
                    // Ensure the values are treated as numbers and pick the maximum
                    $mergedValue = max((int) $valueA, (int) $valueB);

                } elseif ($key === 'remarks') {
                    // Always take remarks from $a
                    $mergedValue = $valueA;

                } else {
                    // Default merging behavior
                    $mergedValue = $valueB ?? $valueA;
                }

                $mergedItem[$key] = $mergedValue;
            }

            $mergedData[] = $mergedItem;
        }

        return $mergedData;
    }

    public function compute($data) {
        for ($i = 1; $i < count($data); $i++) {
            // Compute VL balance
            $current_vl_bal = floatval($data[$i - 1]["vl_bal"]) + floatval($data[$i]["vl_earned"]);

            if ($data[$i]["vl_aut_w_pay"] != "-") {
                $current_vl_bal -= floatval($data[$i]["vl_aut_w_pay"]);
            }

            $data[$i]["vl_bal"] = number_format($current_vl_bal, 3);

            // Compute SL balance
            $current_sl_bal = floatval($data[$i - 1]["sl_bal"]) + floatval($data[$i]["sl_earned"]);

            if ($data[$i]["sl_aut_w_pay"] != "-") {
                $current_sl_bal -= floatval($data[$i]["sl_aut_w_pay"]);
            }

            $data[$i]["sl_bal"] = number_format($current_sl_bal, 3);
        }

        return $data;
    }

    public function triggerSLVLFirst($data) {

        if ($data) {

            $leaveCredits = LeaveCredits::with('leave')->where('employee_no', $data['employee_no'])
                            ->where('leave_type_id', $data['leave_id'])
                            ->first() ?? [];

            $leaveCode = $leaveCredits->leave->code;

            $leaveCreditsBalance = $leaveCredits->credits ?? 0;

            $balance = $leaveCreditsBalance;

            // add code for new employee

            $earned = 1.250;

            $aut_w_pay = 0;

            // Define the months
            $months = [
                'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
                'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
            ];

            // Get the dynamic month and year when the balance should be set to the initial leaveCreditsBalance
            $initialBalanceDate = Carbon::parse($leaveCredits->as_of);
            $initialBalanceMonth = strtoupper($initialBalanceDate->format('F'));
            $initialBalanceYear = $initialBalanceDate->format('Y');

            // Prepare the result array
            $result = [];

            $startAdding = false;
            $currentYear = $initialBalanceYear;

            while (true) {
                foreach ($months as $month) {
                    // Check if it's the month to start including in results
                    if ($month == $initialBalanceMonth && !$startAdding) {
                        $startAdding = true;
                        $balance = $leaveCreditsBalance; // Set initial balance for the first included month
                    } elseif (!$startAdding) {
                        // Skip months before the initial balance month
                        continue;
                    } else {
                        // For each subsequent month, accumulate the balance
                        $balance += $earned; // Add earned leave for the month
                        // Subtract the auto-withheld pay if applicable (only for the month with aut_w_pay)
                        $balance -= $aut_w_pay;
                    }

                    // Add the month data to the result array
                    $result[$leaveCode][] = [
                        'employee_no' => $data['employee_no'],
                        'period' => $month,
                        'particulars' => '',
                        'earned' => number_format($earned, 3),
                        'aut_w_pay' => ($aut_w_pay > 0) ? number_format($aut_w_pay, 3) : '',
                        'bal' => number_format($balance, 3),
                        'year' => $currentYear,
                    ];

                    $aut_w_pay = 0;
                }

                if ($currentYear == Carbon::now()->format('Y')) {
                    break;
                }

                $currentYear++;
            }

            // Return the result

            foreach ($result[$leaveCode] as $data) {
                if($leaveCode == 'VL') {
                    EmployeeLeaveCard::updateOrCreate(
                        ['employee_no' => $data['employee_no'], 'period' => $data['period'], 'year' => $data['year']],
                        [
                            'particulars' => $data['particulars'],
                            'vl_earned' => $data['earned'],
                            'vl_aut_w_pay' => $data['aut_w_pay'],
                            'vl_bal' => $data['bal'],
                            'vl_aut_wo_pay' => '',
                            'year' => $data['year']
                        ]
                    );
                }

                if($leaveCode == 'SL') {
                    EmployeeLeaveCard::updateOrCreate(
                        ['employee_no' => $data['employee_no'], 'period' => $data['period'], 'year' => $data['year']],
                        [
                            'particulars' => $data['particulars'],
                            'sl_earned' => $data['earned'],
                            'sl_aut_w_pay' => $data['aut_w_pay'],
                            'sl_bal' => $data['bal'],
                            'sl_aut_wo_pay' => $data['aut_w_pay'],
                            'year' => $data['year']
                        ]
                    );
                }

            }

        }
    }

    public function leaveCard($employee_no) {

        $records = EmployeeLeaveCard::where('employee_no', $employee_no)->get();

        $sortedRecords = collect($records)
            ->groupBy('year')
            ->map(function ($items, $year) use ($records) {
                $lastItem = $items->last();

                $prevBal = [
                    'vl' => (float)($lastItem['vl_bal'] ?? 0),
                    'sl' => (float)($lastItem['sl_bal'] ?? 0),
                ];

                $previousYearRecord = $records->where('year', $year - 1)->last();

                if ($previousYearRecord) {
                    $prevBal['vl'] = (float)($previousYearRecord['vl_bal'] ?? 0);
                    $prevBal['sl'] = (float)($previousYearRecord['sl_bal'] ?? 0);
                } else {
                    $prevBal['vl'] = 0;
                    $prevBal['sl'] = 0;
                }

                $sortedItems = $items->sortBy(function ($item) {
                    return DateTime::createFromFormat('F', $item['period'])->format('m');
                })->values();

                return [
                    'previous_bal' => $prevBal,
                    'items' => $sortedItems
                ];
            })
            ->sortKeys();

        return $sortedRecords;
    }

}
