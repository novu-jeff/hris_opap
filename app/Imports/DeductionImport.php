<?php

namespace App\Imports;

use App\Models\EmployeeDeductions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DeductionImport implements ToModel, WithStartRow
{

    public $deduction_id;

    public function __construct($deduction_id) {
        $this->deduction_id = $deduction_id;
    }

    public function model(array $row)
    {
        $excelDate = $row[0];
        $date = \Carbon\Carbon::instance(Date::excelToDateTimeObject($excelDate))->format('Y-m-d');

        $existingRecord = EmployeeDeductions::where('employee_no', $row[2])
            ->where('deduction_id', $this->deduction_id)
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
            'deduction_id' => $this->deduction_id,
            'amount' => $row[4],
            'as_of' => $date
        ]);
    }

    public function startRow(): int
    {
        return 5;
    }
}
