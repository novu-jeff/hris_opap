<?php

namespace App\Imports;

use App\Models\EmployeeDeductions;
use App\Models\EmployeeEarnings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EarningImport implements ToModel, WithStartRow
{

    public $earning_id;

    public function __construct($earning_id) {
        $this->earning_id = $earning_id;
    }

    public function model(array $row)
    {
        $excelDate = $row[0];
        $date = \Carbon\Carbon::instance(Date::excelToDateTimeObject($excelDate))->format('Y-m-d');

        $existingRecord = EmployeeEarnings::where('employee_no', $row[2])
            ->where('earning_id', $this->earning_id)
            ->first();

        if ($existingRecord) {
            $existingRecord->update([
                'amount' => $row[4],
                'as_of' => $date
            ]);
            return null; 
        }
        
        return new EmployeeDeductions([
            'employee_no' => trim($row[2]),
            'deduction_id' => $this->earning_id,
            'amount' => $row[4],
            'as_of' => $date
        ]);
    }

    public function startRow(): int
    {
        return 5;
    }
}
