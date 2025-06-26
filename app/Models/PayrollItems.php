<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItems extends Model
{
    use HasFactory;

    protected $table = 'payroll_items';

    protected $fillable = [
        'payroll_id',
        'employee_no',
        'employment_type',
        'name',
        'position',
        'basic_salary',
        'pera',
        'overtime',
        'gross_amount_earned',
        'rlip',
        'hdmf',
        'philhealth',
        'consoloan',
        'emergency_loan',
        'plreg',
        'mpl',
        'cpl',
        'mp2',
        'mplstlms',
        'cir375_cir449',
        'w_tax',
        'uca',
        'aut',
        'total_deductions',
        'net_amount',
        'dbp',
        'kawani',
        'lbp_payroll_account',
        'salary',
    ];

    public function information() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }
}
