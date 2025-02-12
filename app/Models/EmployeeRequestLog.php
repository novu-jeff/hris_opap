<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRequestLog extends Model
{
    use HasFactory;
    
    protected $table = 'employee_request_logs';
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

    public function employee() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function attachments() {
        return $this->hasMany(EmployeeRequestLogAttachments::class, 'employee_requests_id', 'id');
    }

}
