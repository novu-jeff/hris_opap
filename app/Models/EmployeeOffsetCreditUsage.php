<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOffsetCreditUsage extends Model
{
    protected $fillable = [
        'employee_offset_request_id',
        'employee_offset_credit_id',
        'hours_used',
    ];

    public function request()
    {
        return $this->belongsTo(EmployeeOffsetRequest::class);
    }

    public function credit()
    {
        return $this->belongsTo(EmployeeOffsetCredit::class);
    }
}
