<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $table = 'employee_leave';
    protected $fillable = [
        'employee_no',
        'status',
        'leave_id',
        'duration',
        'location',
        'location_specific',
        'confinement',
        'illness',
        'study',
        'study_other_purpose',
        'commutation',
    ];

    public function employment() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function employee() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function leave_type() {
        return $this->hasOne(LeaveType::class, 'id', 'leave_id');
    }

    public function dates() {
        return $this->hasMany(EmployeeLeaveDates::class, 'employee_leave_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(EmployeeLeaveAttachment::class);
    }

}
