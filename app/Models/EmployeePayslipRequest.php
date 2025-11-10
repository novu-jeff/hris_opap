<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayslipRequest extends Model
{
    use HasFactory;

    protected $table = 'employee_payslip_request';
    protected $fillable = [
        'payroll_id',
        'employee_no',
        'status',
        'action_by_id',
        'isDeleted'
    ];

    public function employee() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function payroll() {
        return $this->hasOne(SalaryPayroll::class, 'id', 'payroll_id');
    }

}
