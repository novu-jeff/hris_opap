<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBusinessSlip extends Model
{
    use HasFactory;

    protected $table = 'employee_business_slips';
    protected $fillable = [
        'employee_no',
        'date_filed',
        'destination',
        'purpose',
        'departure_time',
        'arrival_time',
        'requested_by',
        'status',
        'approved_by_id'
    ];

    public function employment() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function employee() {
        return $this->hasOne(EmployeeInformation::class, 'employee_no', 'employee_no');
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function attachments()
    {
        return $this->hasMany(
            EmployeeBusinessSlipAttachment::class,
            'employee_business_slip_id'
        );
    }

}
