<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeAdjustments extends Model
{
    use HasFactory;
    
    protected $table = 'employee_time_adjustments';
    protected $fillable = [
        'employee_no',
        'date',
        'clock_in',
        'break_out',
        'break_in',
        'clock_out',
        'reason',
        'status'
    ];

    public function personal() {
        return $this->hasOne(EmployeePersonal::class, 'employee_no', 'employee_no');
    }

    public function employee() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function attachments() {
        return $this->hasMany(EmployeeTimeAdjustmentsAttachments::class, 'employee_requests_id', 'id');
    }

}
