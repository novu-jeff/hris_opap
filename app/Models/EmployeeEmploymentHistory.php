<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEmploymentHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_employment_history';
    protected $fillable = [
        'employee_no',
        'position',
        'department',
        'company_name',
        'monthly_salary',
        'salary_pay_grade',
        'employment_status',
        'isGovernment',
        'from_year',
        'to_year',
        'documents'
    ];

    public $timestamps = false;
}
