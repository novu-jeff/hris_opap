<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveAttachment extends Model
{
    protected $fillable = [
        'employee_leave_id',
        'attachment',
    ];

    public function leave()
    {
        return $this->belongsTo(EmployeeLeave::class, 'employee_leave_id');
    }
}
