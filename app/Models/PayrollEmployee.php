<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEmployee extends Model
{
    use HasFactory;

    protected $table = 'payroll_employee';
    protected $fillable = [
        'payroll_id',
        'employee_no',
        'position',
        'salary_grade',
        'basic_salary',
        'gross_amount_earned',
        'total_deductions',
        'net_amount',
    ];
    
}
