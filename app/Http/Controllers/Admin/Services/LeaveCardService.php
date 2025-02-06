<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\TimeEquivalent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
    
        // Fetch leave credits, ensuring default values
        $leaveCredits = LeaveCredits::with('leave')
            ->where('employee_no', $data->employee_no)
            ->where('leave_type_id', $data->leave_id)
            ->first();
    
        if (!$leaveCredits || !$leaveCredits->leave) {
            return;
        }
    
        $leaveCode = $leaveCredits->leave->code;
    
        // Leave equivalent deduction
        $leaveEquiv = round((float) $data->daysCovered * 1.00, 3);
        $earned = 1.250; // Leave earned per month
        $aut_w_pay = 0;
    
        // Dates for auto-withheld pay
        $aut_w_pay_month = Carbon::parse($data->date_from)->format('F');
        $aut_w_pay_year = Carbon::parse($data->date_from)->format('Y');
        $prev_month = Carbon::parse($data->date_from)->subMonth()->format('F');
    
        // Get previous month leave balance
        $leaveCardBal = EmployeeLeaveCard::where('employee_no', $data->employee_no)
            ->where('period', strtoupper($prev_month))
            ->where('year', $aut_w_pay_year)
            ->first();
    
        // Set initial balance
        $balance = $leaveCardBal ? ($leaveCode === 'VL' ? (float) $leaveCardBal->vl_bal : (float) $leaveCardBal->sl_bal) : 0;
    

        // Check existing leave records for current month
        $leaveCard = EmployeeLeaveCard::where('employee_no', $data->employee_no)
            ->where('period', strtoupper($aut_w_pay_month))
            ->where('year', $aut_w_pay_year)
            ->first();
    
        if ($leaveCard) {
            $leaveCard_aut_w_pay = $leaveCode === 'VL' ? (float) $leaveCard->vl_aut_w_pay : (float) $leaveCard->sl_aut_w_pay;
            $leaveEquiv += $leaveCard_aut_w_pay;
        }
    
        // Define months for looping
        $months = [
            'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
            'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
        ];
    
        // Store results
        $result = [];
    
        // Get the current month and the previous month
        $currentMonth = strtoupper(Carbon::now()->format('F'));
        $previousMonth = strtoupper(Carbon::now()->subMonth()->format('F'));

        foreach ($months as $month) {
            // Skip the previous month
            if ($month === $previousMonth) {
                continue;  // Skip the previous month (e.g., January)
            }

            // Set auto-withheld pay for the current month
            if ($month === strtoupper($aut_w_pay_month)) {
                $aut_w_pay = $leaveEquiv;
            }

            // Calculate balance
            $balance += $earned;
            $balance -= $aut_w_pay;


            // Generate particulars for the leave records
            $particulars = [];
            if ($data->daysCovered > 1 && $data->from && $data->to) {
                $fromDate = Carbon::parse($data->from);
                $toDate = Carbon::parse($data->to);
                $dateRange = CarbonPeriod::create($fromDate, $toDate);
                foreach ($dateRange as $date) {
                    if ($month === strtoupper($aut_w_pay_month)) {
                        $particulars[] = "{$leaveCode}: " . $date->format('M d');
                    }
                }
            } else if ($month === strtoupper($aut_w_pay_month)) {
                $existingParticulars = $leaveCard->particulars ?? ''; // Get existing particulars or empty string
                $newDate = Carbon::parse($data->from)->format('d'); // Only extract day (without month)
                
                // Extract and group by leave code (e.g., "VL")
                $pattern = "/({$leaveCode}): ([A-Za-z]{3} \d{2})/"; 
                preg_match_all($pattern, $existingParticulars, $matches);
            
                $dates = $matches[2] ?? []; // Extract dates if found
                $dates[] = $newDate; // Add new date
            
                // Remove duplicates and sort dates
                $dates = array_unique($dates);
                sort($dates);
            
                // Get the month name only once
                $monthName = Carbon::parse($data->from)->format('M'); // Get the month (e.g., "Feb")
            
                // Merge the dates into a single formatted string
                $formattedDates = implode(', ', array_map(function($date, $index) use ($monthName) {
                    return $index === 0 ? "{$monthName} {$date}" : $date; // Add month name to the first date only
                }, $dates, array_keys($dates)));
            
                // Format the particulars
                $formattedParticular = "{$leaveCode}: {$formattedDates}";
            
                // Replace the old entry for the same leave type or append if missing
                if (preg_match($pattern, $existingParticulars)) {
                    $updatedParticulars = preg_replace($pattern, $formattedParticular, $existingParticulars);
                } else {
                    $updatedParticulars = trim($existingParticulars . ', ' . $formattedParticular, ', ');
                }
            
                $particulars[] = $updatedParticulars;
            }

            // Store the leave record for the month
            $result[$leaveCode][] = [
                'employee_no' => $data->employee_no,
                'leave_id' => $data->leave_id,
                'period' => $month,
                'particulars' => implode(', ', $particulars),
                'earned' => number_format($earned, 3),
                'aut_w_pay' => $aut_w_pay > 0 ? number_format($aut_w_pay, 3) : '-',
                'bal' => number_format($balance, 3),
                'year' => $aut_w_pay_year,
            ];

            // Reset auto-withheld pay for next month
            $aut_w_pay = 0;
        }
        

        // Save records
        foreach ($result[$leaveCode] as $record) {
            $code = strtolower($leaveCode);
            $updateData = [
                'particulars' => $record['particulars'],
                "{$code}_earned" => $record['earned'],
                "{$code}_aut_w_pay" => $record['aut_w_pay'],
                "{$code}_bal" => $record['bal'],
                "{$code}_aut_wo_pay" => '',
                'year' => $record['year'],
            ];
    
            EmployeeLeaveCard::updateOrCreate(
                [
                    'employee_no' => $record['employee_no'],
                    'period' => $record['period'],
                    'year' => $record['year']
                ],
                $updateData
            );
        }
    
        // Update leave credits for the current month
        $currentMonth = strtoupper(Carbon::now()->format('F'));
        $currentYear = Carbon::now()->format('Y');
    
        foreach ($result[$leaveCode] as $record) {
            if ($record['period'] === $currentMonth && $record['year'] == $currentYear) {
                $newLeaveBalance = $record['bal'];
                $formattedPeriod = Carbon::createFromFormat('F', $record['period'])->format('Y-m');
    
                LeaveCredits::where('employee_no', $record['employee_no'])
                    ->where('leave_type_id', $record['leave_id'])
                    ->update([
                        'credits' => $newLeaveBalance,
                        'as_of' => $formattedPeriod
                    ]);
    
                break;
            }
        }
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
                        'particulars' => '-',
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


}