<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBusinessSlipAttachment extends Model
{
    protected $fillable = [
        'employee_business_slip_id',
        'attachment',
    ];

    public function businessSlip()
    {
        return $this->belongsTo(
            EmployeeBusinessSlip::class,
            'employee_business_slip_id'
        );
    }
}
