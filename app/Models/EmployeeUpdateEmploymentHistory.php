<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUpdateEmploymentHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_update_employment_history';

    protected $fillable = [
        'updating_id',
        'employee_no',
        'position',
        'department',
        'company_name',
        'monthly_salary',
        'employment_status',
        'isGovernment',
        'from_year',
        'to_year',
    ];

    public $timestamps = false;
}
