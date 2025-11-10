<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveDates extends Model
{
    use HasFactory;

    protected $table = 'employee_leave_dates';

    protected $fillable = [
        'employee_leave_id',
        'employee_no',
        'date'
    ];


    public function employeeLeave()
    {
        return $this->belongsTo(EmployeeLeave::class, 'employee_leave_id');
    }

}
