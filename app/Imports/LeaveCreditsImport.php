<?php

namespace App\Imports;

use App\Models\EmployeeLeaveCard;
use App\Models\LeaveCredits;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class LeaveCreditsImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

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

        if($this->isVlSl) {
            return new EmployeeLeaveCard([
                'employee_no'    => is_null($this->employee_no) ? $row[0] : $this->employee_no,
                'year'           => $row[1] ?? '',
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
            return new LeaveCredits([
                'leave_type_id' => $this->leave_type_id,
                'employee_no' => $row[0] ?? '',
                'credits' => $row[1] ?? 0,
                'as_of' => $row[2] ?? '',
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
    

}
