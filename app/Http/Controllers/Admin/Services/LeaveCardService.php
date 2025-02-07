<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\TimeEquivalent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use InvalidArgumentException;

class LeaveCardService extends Controller
{

    public function init(string $employee_no, string $action, $data = null) {
        
        if($employee_no) {

            if($action == 'firstime') {
                $this->formatLeaveCardFirst($data);
            }

            if($action == 'leave_approval') {
                $this->formatLeaveCard($data);
            }

        }

    }

    public function formatLeaveCard($data)
    {
        if (!$data) {
            return;
        }

        $employee_no = $data->employee_no;
        $currentYear = Carbon::now()->year;
        $leaveType = LeaveType::where('id', $data->leave_id)->first();
        $leaveCode = strtolower($leaveType->code);

        $startDate = Carbon::parse($data->from);
        $endDate = $data->to ? Carbon::parse($data->to) : null;

       


        $earned = 1.250;
        $aut_w_pay = 0;
        $aut_wo_pay = 0;

        // CHECK LEAVE BALANCE BASED ON LEAVE CARD BAL

        $leaveCardBalance = EmployeeLeaveCard::where('employee_no', $employee_no)
            ->where('year', $currentYear)
            ->orderBy('year', 'asc')
            ->get()
            ->last();
        
        $leaveCardBalance = $leaveCardBalance ? $leaveCardBalance->{strtolower($leaveCode) . '_bal'} ?? 0 : 0;
    
        $isWOPay = $leaveCardBalance <= 0 ? true : false;

        $leaveMonths = $this->processLeaveMonths($startDate, $endDate, $leaveCode, $isWOPay);

        $months = [
            'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
            'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
        ];
        
        $latestLeaveCard = EmployeeLeaveCard::where('employee_no', $employee_no)
            ->where('year', $currentYear)
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();

        // dd($latestLeaveCard->toArray());
        // dd($leaveMonths);


        $mappedLeaveCards = array_map(function ($leaveCard) use ($leaveMonths, $leaveCode) {
            // Find the corresponding month in the $leaveMonths array
            $matchingMonth = collect($leaveMonths)->firstWhere('month', $leaveCard['period']);
            
            // If a matching month is found, handle the leaveEquiv and particulars
            if ($matchingMonth) {
                // Handle auto pay fields, add if they are already numeric
                $leaveCard[$leaveCode . '_aut_w_pay'] = isset($leaveCard[$leaveCode . '_aut_w_pay']) && is_numeric($leaveCard[$leaveCode . '_aut_w_pay']) 
                    ? $leaveCard[$leaveCode . '_aut_w_pay'] + ($matchingMonth[$leaveCode . '_aut_w_pay'] ?? 0)
                    : ($matchingMonth[$leaveCode . '_aut_w_pay'] ?? '-');
        
                $leaveCard[$leaveCode . '_aut_wo_pay'] = isset($leaveCard[$leaveCode . '_aut_wo_pay']) && is_numeric($leaveCard[$leaveCode . '_aut_wo_pay']) 
                    ? $leaveCard[$leaveCode . '_aut_wo_pay'] + ($matchingMonth[$leaveCode . '_aut_wo_pay'] ?? 0)
                    : ($matchingMonth[$leaveCode . '_aut_wo_pay'] ?? '-');
        
                // Combine the particulars if they already exist
                $leaveCard[$leaveCode . '_particulars'] = isset($leaveCard[$leaveCode . '_particulars']) && $leaveCard[$leaveCode . '_particulars'] !== ''
                ? $leaveCard[$leaveCode . '_particulars'] . ', ' . ($matchingMonth[$leaveCode . '_particulars'] ?? '')
                : ($matchingMonth[$leaveCode . '_particulars'] ?? '-');

            } else {
                // If no matching month found, set all fields to '-'
                $leaveCard[$leaveCode . '_aut_w_pay'] = '-';
                $leaveCard[$leaveCode . '_aut_wo_pay'] = '-';
                $leaveCard[$leaveCode . '_particulars'] = '-';
            }
        
            return $leaveCard;
        }, $latestLeaveCard);
        
        $combined = $this->combine($mappedLeaveCards, $latestLeaveCard);

        // dd($combined);

        $newData = $this->compute($combined);


        foreach($newData as $data) {
            EmployeeLeaveCard::updateOrCreate(
                [
                    'employee_no' => $data['employee_no'],
                    'period' => $data['period'],
                    'year' => $data['year']
                ],
                [
                    'vl_particulars' => $data['vl_particulars'],
                    'vl_earned' => $data['vl_earned'],
                    'vl_aut_w_pay' => $data['vl_aut_w_pay'],
                    'vl_bal' => $data['vl_bal'],
                    'vl_aut_wo_pay' => $data['vl_aut_wo_pay'],
                    'vl_remarks' => null,
                    'sl_earned' => $data['sl_earned'],
                    'sl_aut_w_pay' => $data['sl_aut_w_pay'],
                    'sl_bal' => $data['sl_bal'],
                    'sl_aut_wo_pay' => $data['sl_aut_wo_pay'],
                    'sl_particulars' => $data['sl_particulars'],
                    'sl_remarks' => null,
                ]
            );
        }

    }

    private function processLeaveMonths($startDate, $endDate, $leaveCode, $isWOPay = false) {
        // Ensure that start date is a valid Carbon instance
        if (!$startDate instanceof Carbon) {
            throw new InvalidArgumentException('Start date is not a valid Carbon instance.');
        }
        
        $leaveMonths = [];
        $currentDate = $startDate->copy(); // Ensure we do not modify the original $startDate
    
        // Loop through days from startDate to endDate
        if (!is_null($endDate)) {
            while ($currentDate->lte($endDate)) {
                $month = strtoupper($currentDate->format('F'));  // Get the full month name
                $abbrMonth = $currentDate->format('M');  // Abbreviated month (e.g., 'Jan', 'Feb')
                $day = $currentDate->day;  // Get the day of the month
    
                // Determine the correct key for auto-pay based on $isWOPay
                $payKey = $isWOPay ? $leaveCode . '_aut_wo_pay' : $leaveCode . '_aut_w_pay';
    
                // If the month doesn't exist in the $leaveMonths array, add it
                if (!isset($leaveMonths[$month])) {
                    $leaveMonths[$month] = [
                        'month' => $month,
                        'days' => [$day],  // Store the day in the days array
                        $payKey => round(1.00, 3), // Initialize with 1.0
                        $leaveCode . '_particulars' => strtoupper($leaveCode) . ': ' . $abbrMonth . ' ' . $day
                    ];
                } else {
                    // If the month already exists, just append the day
                    $leaveMonths[$month]['days'][] = $day;
                }
    
                // Move to the next day
                $currentDate->addDay();
            }
    
            // After looping through all the days, calculate the leaveEquiv for each month
            foreach ($leaveMonths as &$entry) {
                // Calculate the number of days before transforming to a string
                $daysCount = count($entry['days']); // Count the days in the array
    
                // Transform days into a comma-separated string
                $entry['days'] = implode(',', $entry['days']);
    
                // Update the leave pay equivalent based on the number of days
                $entry[$payKey] = round($daysCount * 1.00, 3); // Multiply by the number of days
    
                // Update particulars with all the days, properly formatted
                // Fix: use the original month from the array, not the last iteration's month
                // Use abbreviated month for particulars
                $entry[$leaveCode . '_particulars'] = strtoupper($leaveCode) . ': ' . Carbon::parse($entry['month'])->format('M') . ' ' . $entry['days'];
            }
        } else {
            // If endDate is null, treat it as just the startDate
            $month = strtoupper($currentDate->format('F'));
            $abbrMonth = $currentDate->format('M');
            $day = $currentDate->day;
    
            // Determine the correct key for auto-pay based on $isWOPay
            $payKey = $isWOPay ? $leaveCode . '_aut_wo_pay' : $leaveCode . '_aut_w_pay';
    
            $leaveMonths[$month] = [
                'month' => $month,
                'days' => $day,
                $payKey => round(1.00, 3), // Initialize with 1.0
                $leaveCode . '_particulars' => strtoupper($leaveCode) . ': ' . $abbrMonth . ' ' . $day
            ];
        }
    
        return array_values($leaveMonths);  // Re-index the array before returning
    }
    
    private function combine($a, $b) {
        $mergedData = [];
    
        foreach ($a as $index => $itemA) {
            $itemB = $b[$index] ?? []; // Get the corresponding item from arrayB if it exists
            $mergedItem = [];
    
            foreach ($itemA as $key => $valueA) {
                $valueB = $itemB[$key] ?? null;
    
                // List of keys that should be merged uniquely (except numeric values)
                $mergeKeys = ['vl_particulars', 'sl_particulars'];
    
                // Keys that should retain the highest numeric value
                $numericKeys = ['vl_aut_w_pay', 'sl_aut_w_pay', 'vl_aut_wo_pay', 'sl_aut_wo_pay'];
    
                if (in_array($key, $mergeKeys) && $valueB !== null) {
                    // Convert to an array, filter out empty values and '-'
                    $values = array_filter(
                        array_merge(explode(', ', $valueA), explode(', ', $valueB)),
                        fn($v) => $v !== '-' && trim($v) !== ''
                    );
                    // Remove duplicates and reformat
                    $mergedValue = implode(', ', array_unique($values));
    
                } elseif (in_array($key, $numericKeys)) {
                    // Ensure the values are treated as numbers and pick the maximum
                    $mergedValue = max((int) $valueA, (int) $valueB);
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
    
    public function formatLeaveCardFirst($data) {

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
                        'aut_w_pay' => ($aut_w_pay > 0) ? number_format($aut_w_pay, 3) : '-',
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
                            'vl_particulars' => $data['particulars'],
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
                            'sl_particulars' => $data['particulars'],
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


}