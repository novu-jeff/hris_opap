<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSallaryDeduction extends Model
{
    use HasFactory;
     protected $table = 'payroll_salary_deductions';

    protected $fillable = [
        'payroll_item_id', 'reference_type', 'reference_id', 'description', 'amount'
    ];

    public function payrollItem()
    {
        return $this->belongsTo(SalaryItemsPayroll::class, 'payroll_item_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'reference_id');
    }
}