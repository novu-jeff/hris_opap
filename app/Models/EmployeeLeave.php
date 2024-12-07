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
        'reason',
        'from',
        'to',
        'measurement',
        'consumed_hours'
    ];

    public function employment() {
        return $this->hasOne(EmployeeInformation::class, 'id', 'employee_no');
    }

    public function employee() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function leave_type() {
        return $this->hasOne(LeaveType::class, 'id', 'leave_id');
    }

}
