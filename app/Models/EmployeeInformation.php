<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeInformation extends Model
{
    use HasFactory;

    protected $table = 'employee_information';

    protected $fillable = [
        'employee_id',
        'biometrics_id',
        'branch_id',
        'position_id',
        'date_hired',
        'date_resignation',
        'type',
        'status',
        'salary_method',
        'leave_credits',
        'monthly_rate',
        'daily_rate',
        'payroll_account_number',
    ];

}
