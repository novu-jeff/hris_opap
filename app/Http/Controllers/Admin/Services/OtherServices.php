<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Exception;
use PDO;

class OtherServices extends Controller
{

    public function earnings(string $employee_no) {

        $emp = EmployeeInformation::where('employee_no', $employee_no)->first();
        if (!$emp) {
            session()->forget('target');
            return redirect()->route('hris.index');
        }

        $dateHired = format_date($emp->date_hired, 'carbon_date');
        $empJobCategory = $emp->employment_type_id;
        $otherEarnings = OtherEarnings::all();
        $result = [];


        foreach ($otherEarnings as $earning) {
            $eligibleIds = explode(',', $earning->eligible);

            $amount = 0;

            // Calculate the amount based on the amount basis
            switch ($earning->amount_basis) {
                case 'entry':
                    $amount = $earning->amount;
                    break;
                case 'basic_salary':
                    $amount = $emp->monthly_rate;
                    break;
                case 'percentage':
                    $amount = ($earning->amount * $emp->monthly_rate) / 100;
                    break;
                default:
                    throw new Exception("Invalid amount_basis value.");
            }

            // Initial eligibility check based on IDs
            $isEligible = in_array($empJobCategory, $eligibleIds);

            // Initialize the result item
            $resultItem = [
                'code' => $earning->code,
                'name' => $earning->name,
                'amount' => $amount,
                'isEligible' => $isEligible,
            ];

            // Further eligibility checks based on duration and context
            if ($isEligible && $earning->duration && $earning->count && $earning->context && $earning->date) {
                $compareDate = Carbon::parse($earning->date);

                // Adjust compare date based on duration
                switch ($earning->duration) {
                    case 'days':
                        $compareDate->addDays($earning->count);
                        break;
                    case 'months':
                        $compareDate->addMonths($earning->count);
                        break;
                    case 'years':
                        $compareDate->addYears($earning->count);
                        break;
                    default:
                        throw new Exception("Invalid duration type.");
                }

                // Check eligibility based on context
                switch ($earning->context) {
                    case 'from':
                        $isEligible = $dateHired->greaterThanOrEqualTo($compareDate);
                        break;
                    case 'prior_to':
                        $isEligible = $dateHired->lessThan($compareDate);
                        break;
                    case 'subsequent_to':
                        $isEligible = now()->greaterThan($compareDate);
                        break;
                    default:
                        throw new Exception("Invalid context type.");
                }
            }

            // Update eligibility in the result item
            $resultItem['isEligible'] = $isEligible;
            $result[] = $resultItem;
        }

        return $result;
    }

    public function deductions(string $employee_no) {
        
        $otherDeductions = EmployeeDeductions::with('deduction')->where('employee_no', $employee_no)->get()
            ->toArray();
        
        return $otherDeductions ?? [];
    
    }

    public function leaves(string $employee_no) 
    {
        // Get all leave types
        $leaves = LeaveType::all();

        // Get all credits for the given employee
        $credits = LeaveCredits::where('employee_no', $employee_no)->get();

        // Map leave credits and calculate totals for specific leave types
        $leaveCredits = $leaves->map(function ($leave) use ($credits, $employee_no) {

            // If leave type is VL or SL (ID 1 or 2), get balance from EmployeeLeaveCard
            if ($leave->id == 1 || $leave->id == 2) {
                // Get the latest leave card record for the employee for the current year
                $record = EmployeeLeaveCard::where('employee_no', $employee_no)
                    ->where('year', Carbon::now()->year)
                    ->orderBy('year', 'asc') 
                    ->get()
                    ->last();

                // Set leave total credits, fallback to 0 if not found
                $leave->credits = $record ? ($record->{strtolower($leave->code) . '_bal'} ?? 0) : 0;
            } else {
                // Otherwise, use the credits from the LeaveCredits table
                $credit = $credits->firstWhere('leave_type_id', $leave->id);
                $leave->credits = $credit ? $credit->credits : 0;
            }

            return $leave;
        });

        // Convert the collection to an array and return
        return $leaveCredits->toArray();
    }


    
}