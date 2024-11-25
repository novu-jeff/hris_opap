<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInformation;
use App\Models\OtherDeductions;
use App\Models\OtherEarnings;
use Carbon\Carbon;

class OtherEligibilityService extends Controller
{
    public function earnings(int $id)
    {

        $emp = EmployeeInformation::where('employee_no', $id)->first();

        $dateHired = $emp->date_hired;
        $emp_job_cat = $emp->job_category_id;
        $otherEarnings = OtherEarnings::all();
        $result = [];

        foreach ($otherEarnings as $earnings) {

            $eligibleIds = explode(',', $earnings->eligible);
        
            $amount_basis = $earnings->amount_basis;

            if($amount_basis == 'entry') {
                $amount = $earnings->amount;
            } else if($amount_basis == 'basic_salary') {
                $amount = $emp->monthly_rate;
            } else if($amount_basis == 'percentage') {
                $amount = ($earnings->amount * $emp->monthly_rate) / 100;
            }

            // Initial eligibility check based on IDs
            $isEligible = in_array($emp_job_cat, $eligibleIds);
        
            // Initialize result array with eligibility
            $resultItem = [
                'code' => $earnings->code,
                'name' => $earnings->name,
                'amount' => $amount,
                'isEligible' => $isEligible,
            ];
        
            // If eligible and all duration-related fields are provided, check further
            if ($isEligible && $earnings->duration && $earnings->count && $earnings->context && $earnings->date) {
                $currentDate = now();
                $hireDate = Carbon::parse($dateHired); // Parse the hire date using Carbon
        
                // Check if the hire date is valid
                if (!$hireDate->isValid()) {
                    // Handle the invalid date case (e.g., set eligibility to false or skip)
                    $isEligible = false;
                    $resultItem['isEligible'] = $isEligible;
                    $result[] = $resultItem;
                    continue; // Skip further processing for invalid date
                }
        
                $compareDate = null;
        
                // Calculate compare date based on duration

                switch ($earnings->duration) {
                    case 'days':
                        $compareDate = $hireDate->copy()->addDays($earnings->count);
                        break;
        
                    case 'months':
                        $compareDate = $hireDate->copy()->addMonths($earnings->count);
                        break;
        
                    case 'years':
                        $compareDate = $hireDate->copy()->addYears($earnings->count);
                        break;
        
                    default:
                        throw new Exception('Invalid duration type.');
                }
        
                // Update eligibility based on date comparison
                if ($compareDate && $currentDate->greaterThanOrEqualTo($compareDate)) {
                    $isEligible = true;
                } else {
                    $isEligible = false;
                }
            }
        
            // Update the result item with final eligibility status
            $resultItem['isEligible'] = $isEligible;
            $result[] = $resultItem;
        }
        
        return $result;        
    }

    public function deductions(int $id) {

        $record = EmployeeInformation::find($id);
        
        $otherDeductions = OtherDeductions::all();

        $result = [];
        
        foreach ($otherDeductions as $deductions) {
            $result[] = $deductions->name;
        }

        return [];
    }

    
}