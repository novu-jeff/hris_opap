<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeEarnings;
use App\Models\EmployeeInformation;
use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use App\Models\LeaveType;
use App\Models\OtherEarnings;
use Carbon\Carbon;
use Exception;
use PDO;
use Illuminate\Support\Facades\Log;

class OtherServices extends Controller
{

   /* public function earnings(string $employee_no) {

        $records = EmployeeEarnings::where('employee_no', $employee_no)
            ->get();

        $newRecords = [];


        foreach($records as $record) {
            
            if($record['amount_type'] == 'fixed_amount') {
                $newRecords[] = [
                    'amount_type' => 'fixed_amount',
                    'first_term' => $record->first_term,
                    'second_term' => $record->second_term,
                ];
            }

            if($record['amount_type'] == 'percentage') {
                $newRecords[] = [
                    'amount_type' => 'fixed_amount',
                    'first_term' => $record->first_term,
                    'second_term' => $record->second_term,
                ];
            }

            if($record['amount_type'] == 'basic_salary') {
                $newRecords[] = [
                    'amount_type' => 'basic_salary',
                    'first_term' => $record->first_term,
                    'second_term' => $record->second_term,
                ];
            }

        }

        return $records;
    }*/

    public function earnings(string $employee_no): array
{
    $records = EmployeeEarnings::with('earning') // load relation to OtherEarnings
        ->where('employee_no', $employee_no)
        ->get();

    $newRecords = [];

    foreach ($records as $record) {
        $newRecords[] = [
            'code' => $record->earning->code ?? 'UNKNOWN',
            'name' => $record->earning->name ?? 'Unknown',
            'amount_type' => $record->amount_type,
            'first_term' => $record->first_term ?? 0,
            'second_term' => $record->second_term ?? 0,
            'amount' => $record->amount ?? 0,
        ];
    }

    return $newRecords;
}


    public function deductions(string $employee_no) {

      
        
        $records = EmployeeDeductions::with('deduction') // load relation to OtherEarnings
        ->where('employee_no', $employee_no)
        ->get();

        $newRecords = [];

        foreach ($records as $record) {
            $newRecords[] = [
                'code' => $record->deduction->code ?? 'UNKNOWN',
                'name' => $record->deduction->name ?? 'Unknown',
                'amount' => $record->amount ?? 0,
            ];
        }

        return $newRecords;
    
    }

    public function leaves(string $employee_no) 
    {
        # Get all leave types
        $leaves = LeaveType::all();

        # Get all credits for the given employee
        $credits = LeaveCredits::where('employee_no', $employee_no)->get();

        # Map leave credits and calculate totals for specific leave types
        $leaveCredits = $leaves->map(function ($leave) use ($credits, $employee_no) {

            # If leave type is VL or SL (ID 1 or 2), get balance from EmployeeLeaveCard
            if ($leave->id == 1 || $leave->id == 2) {
                # Get the latest leave card record for the employee for the current year
                $record = EmployeeLeaveCard::where('employee_no', $employee_no)
                    ->where('year', Carbon::now()->year)
                    ->orderBy('year', 'asc') 
                    ->get()
                    ->last();

                # Set leave total credits, fallback to 0 if not found
                $leave->credits = $record ? ($record->{strtolower($leave->code) . '_bal'} ?? 0) : 0;
            } else {
                # Otherwise, use the credits from the LeaveCredits table
                $credit = $credits->firstWhere('leave_type_id', $leave->id);
                $leave->credits = $credit ? $credit->credits : 0;
            }

            return $leave;
        });

        # Convert the collection to an array and return
        return $leaveCredits->toArray();
    }

    public function splitDateRange(string $range): array
    {
        return array_map('trim', explode('to', $range));
    }
    
}