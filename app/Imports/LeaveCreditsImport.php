<?php

namespace App\Imports;

use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Exception;

class LeaveCreditsImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    public $employee_no;
    public $isVlSl;
    public $leave_type_id;

    public function __construct($employee_no, $isVlSl, $leave_type_id)
    {
        $this->employee_no = $employee_no;
        $this->isVlSl = $isVlSl;
        $this->leave_type_id = $leave_type_id;
    }

    public function model(array $row)
    {
        if ($this->isVlSl) {
            $employeeNo = is_null($this->employee_no) ? $row[0] : $this->employee_no;
            $year = $row[1] ?? '';

            // ✅ Check if record already exists
            $exists = EmployeeLeaveCard::where('employee_no', $employeeNo)
                ->where('year', $year)
                ->exists();

            if ($exists) {
                Log::warning("Duplicate year found for employee_no {$employeeNo}, year {$year}");
                // Throw an exception so the controller can handle it
                throw new Exception("Year {$year} already exists for employee {$employeeNo}");
            }

            // ✅ Create a new record if no duplicate
            return new EmployeeLeaveCard([
                'employee_no'    => $employeeNo,
                'year'           => $year,
                'period'         => $row[2] ?? '',
                'particulars'    => $row[3] ?? '',
                'vl_earned'      => $row[4] ?? 0,
                'vl_aut_w_pay'   => $row[5] ?? 0,
                'vl_bal'         => $row[6] ?? 0,
                'vl_aut_wo_pay'  => $row[7] ?? 0,
                'sl_earned'      => $row[8] ?? 0,
                'sl_aut_w_pay'   => $row[9] ?? 0,
                'sl_bal'         => $row[10] ?? 0,
                'sl_aut_wo_pay'  => $row[11] ?? 0,
                'remarks'        => $row[12] ?? '',
            ]);
        } else {
            // ✅ For Leave Credits import
            return new LeaveCredits([
                'leave_type_id' => $this->leave_type_id,
                'employee_no'   => $row[0] ?? '',
                'credits'       => $row[1] ?? 0,
                'as_of'         => $row[2] ?? '',
            ]);
        }
    }

    public function startRow(): int
    {
        return 2; // Skip header row
    }
}
