<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOvertime extends Model
{
    protected $table = 'employee_overtimes';

    protected $fillable = [
        'employee_no',
        'work_date',
        'total_minutes',
        'required_minutes',
        'overtime_minutes',
        'overtime_hours',
        'scheduled_out',
        'actual_out',
        'status',
        'remarks',
    ];

}    
