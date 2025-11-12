<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTimeAdjustmentsAttachments extends Model
{
    use HasFactory;

    protected $table = 'employee_time_adjustments_attach';
    protected $fillable = [
        'employee_requests_id',
        'attachment',
    ];

    public $timestamps = false;

}
